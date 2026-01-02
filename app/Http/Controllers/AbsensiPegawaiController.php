<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiPegawai;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class AbsensiPegawaiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', AbsensiPegawai::class);

            $tahun = $request->tahun ?? now()->year;
            $currentMonth = now()->month;
            $tw = $request->tw ?? ceil($currentMonth / 3);

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
                ->orderBy('id_madrasah')
                ->orderBy('nama_rekening')
                ->get();

            $pegawaiList = $pegawaiList->map(function ($p) use ($bulanTW) {
                $absensi = $p->absensi->keyBy('bulan');

                return (object) [
                    'nama_madrasah' => $p->madrasah->nama_madrasah ?? '-',
                    'nama_rekening' => $p->nama_rekening,
                    'bulan' => collect($bulanTW)->mapWithKeys(function ($b) use ($absensi) {
                        $a = $absensi[$b] ?? null;
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

            $totalPerBulan = [];
            foreach ($pegawaiList as $pegawai) {
                foreach ($pegawai->bulan as $bulan => $data) {
                    foreach ($data as $k => $v) {
                        $totalPerBulan[$bulan][$k] = ($totalPerBulan[$bulan][$k] ?? 0) + $v;
                    }
                }
            }

            // ===== SUKSES =====
            Log::info('Akses halaman absensi pegawai', [
                'user_id' => auth()->id(),
                'tahun' => $tahun,
                'tw' => $tw,
                'ip' => $request->ip(),
            ]);

            return view('absensi.index', [
                'pegawaiList'   => $pegawaiList,
                'tw'            => $tw,
                'bulanTriwulan' => array_map(fn ($b) => $namaBulan[$b], $bulanTW),
                'totalPerBulan' => $totalPerBulan,
            ]);
        } catch (Throwable $e) {
            // ===== ERROR =====
            Log::error('Gagal membuka halaman absensi pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            abort(500, 'Terjadi kesalahan sistem.');
        }
    }

    public function create(Request $request)
    {
        try {
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

            $absensiExisting = AbsensiPegawai::where('tahun', $tahun)
                ->where('tw', $tw)
                ->get()
                ->groupBy(['pegawai_id', 'bulan']);

            // ===== SUKSES =====
            Log::info('Akses form input absensi', [
                'user_id' => auth()->id(),
                'tahun' => $tahun,
                'tw' => $tw,
                'ip' => $request->ip(),
            ]);

            return view('absensi.create', compact(
                'pegawaiList',
                'bulan',
                'bulanAngka',
                'absensiExisting',
                'tw',
                'tahun'
            ));
        } catch (Throwable $e) {
            // ===== ERROR =====
            Log::error('Gagal membuka form input absensi', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            abort(500, 'Terjadi kesalahan sistem.');
        }
    }

    public function store(Request $request)
    {
        try {
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
                                'bulan' => $bulan,
                                'tahun' => $tahun,
                                'tw' => $tw,
                            ],
                            [
                                'sakit' => (int) ($data['sakit'] ?? 0),
                                'izin' => (int) ($data['izin'] ?? 0),
                                'ketidakhadiran' => (int) ($data['ketidakhadiran'] ?? 0),
                                'dinas_luar' => (int) ($data['dinas_luar'] ?? 0),
                                'cuti' => (int) ($data['cuti'] ?? 0),
                            ]
                        );
                    }
                }
            });

            // ===== SUKSES =====
            Log::info('Absensi pegawai berhasil disimpan', [
                'user_id' => auth()->id(),
                'tahun' => $tahun,
                'tw' => $tw,
                'ip' => $request->ip(),
            ]);

            return back()->with('swal_success', 'Absensi berhasil disimpan / diperbarui.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            // ===== ERROR =====
            Log::error('Gagal menyimpan absensi pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return back()->with('swal_error', 'Gagal menyimpan absensi. Silakan coba lagi.');
        }
    }

    public function export(Request $request)
    {
        try {
            $tahun = $request->tahun ?? now()->year;
            $tw = $request->tw ?? 1;

            // ===== SUKSES =====
            Log::info('Export absensi pegawai', [
                'user_id' => auth()->id(),
                'tahun' => $tahun,
                'tw' => $tw,
                'ip' => $request->ip(),
            ]);

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\AbsensiPegawaiExport($tahun, $tw),
                "absensi_pegawai_TW{$tw}_{$tahun}.xlsx"
            );
        } catch (Throwable $e) {
            // ===== ERROR =====
            Log::error('Gagal export absensi pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return back()->with('swal_error', 'Gagal export data absensi.');
        }
    }
}
