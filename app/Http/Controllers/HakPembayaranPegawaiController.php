<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Madrasah;
use App\Models\HakPembayaranPegawai;
use App\Models\AttendancePeriod;

class HakPembayaranPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $activePeriod = AttendancePeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            abort(404, 'Belum ada periode aktif.');
        }

        $madrasahId = $request->madrasah;

        $tw = $activePeriod->tw_number;
        $tahun = $activePeriod->tahun;

        $madrasahList = Madrasah::orderBy('nama_madrasah')->get();

        $bulan = $activePeriod->bulan_list;

        // =========================
        // QUERY PEGawai (UNIFIED)
        // =========================
        $query = Pegawai::with('madrasah');

        if (auth()->user()->hasRole('operator')) {

            $madrasahIdUser = auth()->user()->unit_kerja;

            if (!$madrasahIdUser) {
                abort(403, 'Operator belum memiliki madrasah.');
            }

            $query->where('id_madrasah', $madrasahIdUser);

            // ❗ OPERATOR: FULL DATA (untuk modal)
            $pegawai = $query
                ->orderBy('id_madrasah')
                ->orderBy('nama_rekening')
                ->get();

        } else {

            if ($request->madrasah) {
                $query->where('id_madrasah', $request->madrasah);
            }

            // ❗ SUPERADMIN: PAGINATE
            $pegawai = $query
                ->orderBy('id_madrasah')
                ->orderBy('nama_rekening')
                ->get();
                // ->paginate(25)
                // ->withQueryString();
        }

        // =========================
        // HAK PEMBAYARAN MAP
        // =========================
        $hakRaw = HakPembayaranPegawai::where('tahun', $tahun)
            ->whereIn('bulan', $bulan)
            ->get()
            ->groupBy('pegawai_id');

        $hakMap = [];

        foreach ($hakRaw as $pegawaiId => $items) {
            foreach ($items as $item) {
                $hakMap[$pegawaiId][$item->bulan] = (int) $item->jumlah_hak;
            }
        }

        return view('hak_pegawai.index', [
            'activePeriod' => $activePeriod,
            'madrasahList' => $madrasahList,
            'pegawai' => $pegawai,
            'bulan' => $bulan,
            'tw' => $tw,
            'tahun' => $tahun,
            'hakMap' => $hakMap,
        ]);
    }

    public function store(Request $request)
    {
        $activePeriod = AttendancePeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            return back()->withErrors('Belum ada periode aktif.');
        }

        $tahun = $activePeriod->tahun;
        $tw = $activePeriod->tw_number;

        $bulanList = $activePeriod->bulan_list;

        $request->validate([
            'hak' => 'nullable|array',
            'hak.*' => 'array',
        ]);

        $hak = $request->hak ?? [];

        // Pegawai yang dikirim harus pegawai yang boleh diakses user ini.
        // Global scope Pegawai sudah membatasi ke madrasah operator (dan status aktif).
        $pegawaiIds = array_keys($hak);
        $jumlahValid = Pegawai::whereIn('id', $pegawaiIds)->count();

        if ($jumlahValid !== count($pegawaiIds)) {
            abort(403, 'Terdapat pegawai yang bukan bagian dari madrasah Anda.');
        }

        foreach ($hak as $pegawaiId => $bulanData) {

            $adaDipilih = collect($bulanData)->contains(1);

            if (!$adaDipilih) {
                return back()->withErrors(
                    "Pegawai ID $pegawaiId belum diisi minimal 1 bulan"
                );
            }
        }

        foreach ($hak as $pegawaiId => $bulanData) {

            foreach ($bulanData as $bulan => $status) {

                if (!in_array((int)$bulan, $bulanList)) {
                    continue;
                }

                HakPembayaranPegawai::updateOrCreate(
                    [
                        'pegawai_id' => $pegawaiId,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'tw' => $tw,
                    ],
                    [
                        'jumlah_hak' => $status ? 1 : 0,
                    ]
                );
            }
        }

        return back()->with('success', 'Data berhasil disimpan');
    }
}