<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiPegawai;
use App\Models\Pegawai; // import model Pegawai
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AbsensiPegawaiController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('viewAny', AbsensiPegawai::class);

        $tahun = $request->tahun ?? now()->year;
        $currentMonth = now()->month;
        $tw = $request->tw ?? ceil($currentMonth / 3);

        // Bulan angka per TW
        $bulanAngka = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $bulanTW = $bulanAngka[$tw];

        $pegawaiList = Pegawai::whereHas('absensi', function ($q) use ($tahun, $bulanTW) {
                    $q->where('tahun', $tahun)
                    ->whereIn('bulan', $bulanTW);
                })
                ->with([
                    'madrasah',
                    'absensi' => fn ($q) =>
                        $q->where('tahun', $tahun)
                        ->whereIn('bulan', $bulanTW)
                ])
                ->orderBy('id_madrasah', 'asc')
                ->orderBy('nama_rekening', 'asc')
                ->get();

        // Mapping data supaya blade enak
        $pegawaiList = $pegawaiList->map(function ($p) use ($bulanTW) {
            $absensi = $p->absensi->keyBy('bulan');

            return (object) [
                'nama_madrasah' => $p->madrasah->nama_madrasah ?? '-',
                'nama_rekening' => $p->nama_rekening,

                'bulan' => collect($bulanTW)->mapWithKeys(function ($b) use ($absensi) {
                    $a = $absensi[$b] ?? null; // aman jika kosong
                    return [
                        $b => [
                            's'  => $a->sakit ?? 0,
                            'i'  => $a->izin ?? 0,
                            'kt' => $a->ketidakhadiran ?? 0,
                            'dl' => $a->dinas_luar ?? 0,
                            'c'  => $a->cuti ?? 0,
                        ]
                    ];
                })
            ];
        });

        // Total keseluruhan
        $totalAbsensi = [
            's'  => $pegawaiList->sum(fn ($p) => $p->bulan->sum('s')),
            'i'  => $pegawaiList->sum(fn ($p) => $p->bulan->sum('i')),
            'kt' => $pegawaiList->sum(fn ($p) => $p->bulan->sum('kt')),
            'dl' => $pegawaiList->sum(fn ($p) => $p->bulan->sum('dl')),
            'c'  => $pegawaiList->sum(fn ($p) => $p->bulan->sum('c')),
        ];

        $totalPerBulan = [];

        foreach ($pegawaiList as $pegawai) {
            foreach ($pegawai->bulan as $bulan => $data) {
                $totalPerBulan[$bulan]['s']  = ($totalPerBulan[$bulan]['s']  ?? 0) + $data['s'];
                $totalPerBulan[$bulan]['i']  = ($totalPerBulan[$bulan]['i']  ?? 0) + $data['i'];
                $totalPerBulan[$bulan]['kt'] = ($totalPerBulan[$bulan]['kt'] ?? 0) + $data['kt'];
                $totalPerBulan[$bulan]['dl'] = ($totalPerBulan[$bulan]['dl'] ?? 0) + $data['dl'];
                $totalPerBulan[$bulan]['c']  = ($totalPerBulan[$bulan]['c']  ?? 0) + $data['c'];
            }
        }

        return view('absensi.index', [
            'pegawaiList'   => $pegawaiList,
            'tw'            => $tw,
            'bulanTriwulan' => array_map(fn ($b) => $namaBulan[$b], $bulanTW),
            // 'totalAbsensi'  => $totalAbsensi,
            'totalPerBulan' => $totalPerBulan,
        ]);
    }


    public function create(Request $request)
    {
        $this->authorize('viewAny', AbsensiPegawai::class);

        $tahun = $request->tahun ?? now()->year;
        $tw = $request->tw ?? ceil(now()->month / 3);

        $bulanPerTW = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $bulanAngka = $bulanPerTW[$tw];
        $bulan = array_map(fn ($b) => $namaBulan[$b], $bulanAngka);

        $pegawaiList = Pegawai::orderBy('nama_rekening')->get();

        // 🔥 Ambil absensi yg sudah ada
        $absensiExisting = AbsensiPegawai::where('tahun', $tahun)
            ->where('tw', $tw)
            ->get()
            ->groupBy(['pegawai_id', 'bulan']);

        return view('absensi.create', compact(
            'pegawaiList',
            'bulan',
            'bulanAngka',
            'absensiExisting',
            'tw',
            'tahun'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AbsensiPegawai::class);

        $request->validate([
            'tahun' => 'required|integer',
            'tw' => 'required|integer|min:1|max:4',
            'absensi' => 'array'
        ]);

        $tahun = $request->tahun;
        $tw = $request->tw;
        $absensiData = $request->input('absensi', []);

        DB::transaction(function () use ($absensiData, $tahun, $tw) {

            foreach ($absensiData as $pegawaiId => $bulanList) {

                foreach ($bulanList as $bulan => $data) {

                    AbsensiPegawai::updateOrCreate(
                        [
                            'pegawai_id' => $pegawaiId,
                            'bulan'      => $bulan,
                            'tahun'      => $tahun,
                            'tw'         => $tw, // tambahkan ini
                        ],
                        [
                            'sakit'           => (int) ($data['sakit'] ?? 0),
                            'izin'            => (int) ($data['izin'] ?? 0),
                            'ketidakhadiran'  => (int) ($data['ketidakhadiran'] ?? 0),
                            'dinas_luar'      => (int) ($data['dinas_luar'] ?? 0),
                            'cuti'            => (int) ($data['cuti'] ?? 0),
                        ]
                    );
                }
            }
        });

        return back()->with('swal_success', 'Absensi berhasil disimpan / diperbarui.');
    }

    public function export(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;
        $tw = $request->tw ?? 1; // default TW 1

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AbsensiPegawaiExport($tahun, $tw),
            "absensi_pegawai_TW{$tw}_{$tahun}.xlsx"
        );
    }


}
