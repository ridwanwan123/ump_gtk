<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use App\Models\AbsensiPegawai;
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

        $tahunList = AbsensiPegawai::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $bulanList = AbsensiPegawai::select('bulan')
            ->distinct()
            ->orderBy('bulan')
            ->pluck('bulan');

        if ($request->tahun) {
            $bulanList = AbsensiPegawai::where('tahun', $request->tahun)
                ->select('bulan')
                ->distinct()
                ->orderBy('bulan')
                ->pluck('bulan');
        }

        $data = [];

        // default agar tidak error
        $isMissingHakPembayaran = false;
        $missingMadrasah = [];

        if ($request->has(['bulan', 'tahun', 'honor'])) {

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
                    $request->bulan,
                    $request->tahun,
                    $request->honor
                );

                $data[] = $result;

                // =========================
                // AGREGASI WARNING
                // =========================
                if ($result['is_missing_hak_pembayaran']) {

                    $isMissingHakPembayaran = true;

                    $missingMadrasah[$pegawai->madrasah->nama_madrasah] = true;
                }
            }
        }

        return view('rekap-honor.index', [
            'madrasahs' => $madrasahs,
            'jabatanList' => $jabatanList,
            'data' => $data,
            'tahunList' => $tahunList,
            'bulanList' => $bulanList,

            // ✅ INI YANG SEBELUMNYA ERROR
            'isMissingHakPembayaran' => $isMissingHakPembayaran,
            'missingMadrasah' => array_keys($missingMadrasah),
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(
            new RekapHonorExport(
                $request->bulan,
                $request->tahun,
                $request->honor
            ),
            'rekap-honor.xlsx'
        );
    }
}