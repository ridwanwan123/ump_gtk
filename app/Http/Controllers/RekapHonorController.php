<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use App\Models\AttendancePeriod;
use Illuminate\Http\Request;
use App\Services\RekapHonorService;
use App\Exports\RekapHonorExport;
use Maatwebsite\Excel\Facades\Excel;

class RekapHonorController extends Controller
{
    public function index(Request $request, RekapHonorService $service)
    {
        $madrasahs = Madrasah::all();

        $jabatanList = Pegawai::select('jabatan_ump')
            ->distinct()
            ->pluck('jabatan_ump');

        $activePeriod = AttendancePeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            abort(404, 'Belum ada periode aktif.');
        }

        $tahunList = collect([$activePeriod->tahun]);

        $bulanList = collect($activePeriod->bulan_list);

        $bulanNama = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // dd($bulanList);

        $tahun = $request->tahun ?? $activePeriod->tahun;

        $bulan = $request->bulan ?? $bulanList->toArray();

        $data = [];

        // default agar tidak error
        $isMissingHakPembayaran = false;
        $missingMadrasah = [];

        if ($request->filled('honor')) {

            $query = Pegawai::with('madrasah')
                ->join('madrasah', 'pegawai.id_madrasah', '=', 'madrasah.id')
                ->orderBy('madrasah.nama_madrasah', 'asc')
                ->orderBy('pegawai.nama_rekening', 'asc')
                ->select('pegawai.*');

            if ($request->madrasah) {
                $query->where('id_madrasah', $request->madrasah);
            }

            if ($request->jabatan_ump) {
                $query->where('jabatan_ump', $request->jabatan_ump);
            }

            $pegawaiList = $query->get();

            foreach ($pegawaiList as $pegawai) {

                $result = $service->hitung(
                    $pegawai,
                    $bulan,
                    $tahun,
                    $request->honor
                );

                $data[] = $result;

                // =========================
                // AGREGASI WARNING
                // =========================
                if ($result['is_missing_hak_pembayaran']) {
                    $isMissingHakPembayaran = true;

                    $namaMadrasah = $pegawai->madrasah->nama_madrasah;

                    if (!isset($missingMadrasah[$namaMadrasah])) {
                        $missingMadrasah[$namaMadrasah] = 0;
                    }

                    $missingMadrasah[$namaMadrasah]++;
                }
            }
        }


        return view('rekap-honor.index', [
            'madrasahs' => $madrasahs,
            'jabatanList' => $jabatanList,
            'data' => $data,
            'tahunList' => $tahunList,
            'bulanList' => $bulanList,
            'bulanNama' => $bulanNama,
            'triwulanAktif' => $activePeriod->triwulan,

            // ✅ INI YANG SEBELUMNYA ERROR
            'isMissingHakPembayaran' => $isMissingHakPembayaran,
            'missingMadrasah' => $missingMadrasah,
        ]);
    }

    public function export(Request $request)
    {
        $activePeriod = AttendancePeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            abort(404, 'Belum ada periode aktif.');
        }

        $request->validate([
            'honor'       => 'required|numeric|min:0',
            'tahun'       => 'nullable|integer',
            'bulan'       => 'nullable|array',
            'bulan.*'     => 'integer|between:1,12',
            'madrasah'    => 'nullable|integer',
            'jabatan_ump' => 'nullable|string',
        ]);

        // Sama seperti halaman rekap: tanpa pilihan -> periode aktif.
        // Filter madrasah & jabatan ikut dipakai supaya isi file sama dengan yang tampil di layar.
        return Excel::download(
            new RekapHonorExport(
                $request->bulan ?? $activePeriod->bulan_list,
                $request->tahun ?? $activePeriod->tahun,
                $request->honor,
                $request->madrasah,
                $request->jabatan_ump
            ),
            'rekap-honor.xlsx'
        );
    }
}