<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiPegawai;
use App\Models\AttendancePeriod;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class AbsensiPegawaiController extends Controller
{
    private $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function __construct()
    {
        $this->authorizeResource(AbsensiPegawai::class, 'absensi');
    }

    /**
     * Rekap absensi. Ditampilkan dinamis: kalau periode (tahun+tw) yang diminta
     * masih aktif, kolom bulan yang muncul hanya sampai bulan_aktif saat ini
     * (bulan_terbuka). Kalau periode itu sudah tidak aktif lagi (TW lampau),
     * tampilkan penuh 3 bulan triwulan tsb.
     */
    public function index(Request $request)
    {
        try {
            $activePeriod = AttendancePeriod::where('is_active', true)->first();

            if (!$activePeriod) {
                abort(404, 'Belum ada periode aktif.');
            }

            $tahun = $request->tahun ?? $activePeriod->tahun;
            $tw = $request->tw ?? $activePeriod->tw_number;

            // Cari periode sesuai tahun & tw yang diminta (bisa periode aktif / periode lampau)
            $period = AttendancePeriod::where('tahun', $tahun)
                ->where('triwulan', 'TW ' . $tw)
                ->first();

            $bulanAngkaPerTW = [
                1 => [1, 2, 3],
                2 => [4, 5, 6],
                3 => [7, 8, 9],
                4 => [10, 11, 12],
            ];

            if ($period && $period->is_active) {
                // periode masih berjalan -> bulan yang BOLEH ditampilkan hanya
                // sampai bulan_aktif yang sudah ditentukan superadmin
                $bulanTersedia = $period->bulan_terbuka;
            } else {
                // periode sudah lewat / tidak ditemukan -> seluruh bulan TW tsb sudah "tertutup", boleh ditampilkan penuh
                $bulanTersedia = $bulanAngkaPerTW[$tw] ?? [];
            }

            // Filter tambahan: user boleh pilih SATU bulan spesifik dari bulan
            // yang tersedia (bulan_terbuka). Validasi in_array() memastikan
            // user tidak bisa "mengintip" bulan yang belum dibuka superadmin
            // lewat parameter GET.
            if ($request->has('bulan')) {
                // Form filter sudah disubmit -> hormati pilihan user apa adanya
                // (termasuk kalau dia sengaja pilih "Semua Bulan (Progresif)").
                $bulanFilter = $request->filled('bulan') ? (int) $request->bulan : null;
            } else {
                // Halaman baru dibuka / belum ada filter disubmit sama sekali
                // -> default langsung ke bulan yang sedang aktif (bulan_aktif).
                $bulanFilter = ($period && $period->bulan_aktif) ? (int) $period->bulan_aktif : null;
            }

            if ($bulanFilter && in_array($bulanFilter, $bulanTersedia, true)) {
                $bulanTW = [$bulanFilter];
            } else {
                $bulanFilter = null;
                $bulanTW = $bulanTersedia;
            }

            $opsiBulan = array_map(
                fn ($b) => ['value' => $b, 'label' => $this->namaBulan[$b]],
                $bulanTersedia
            );

            if (empty($bulanTW)) {
                return view('absensi.index', [
                    'pegawaiList'   => collect(),
                    'tw'            => $tw,
                    'bulanTriwulan' => [],
                    'totalPerBulan' => [],
                    'opsiBulan'     => $opsiBulan,
                    'bulanFilter'   => $bulanFilter,
                ]);
            }

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
                'bulan_ditampilkan' => $bulanTW,
                'ip' => $request->ip(),
            ]);

            return view('absensi.index', [
                'pegawaiList'   => $pegawaiList,
                'tw'            => $tw,
                'bulanTriwulan' => array_map(fn ($b) => $this->namaBulan[$b], $bulanTW),
                'totalPerBulan' => $totalPerBulan,
                'opsiBulan'     => $opsiBulan,
                'bulanFilter'   => $bulanFilter,
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

    /**
     * Form input HANYA untuk satu bulan: bulan_aktif dari periode yang sedang aktif.
     * Ini menggantikan form lama yang render 3 bulan sekaligus (gelondongan).
     */
    public function create(Request $request)
    {
        try {
            $activePeriod = AttendancePeriod::where('is_active', true)->first();

            if (!$activePeriod) {
                abort(404, 'Belum ada periode aktif.');
            }

            if (!$activePeriod->bulan_aktif) {
                abort(404, 'Admin belum menentukan bulan aktif untuk periode ini.');
            }

            $tahun = $activePeriod->tahun;
            $tw = $activePeriod->tw_number;
            $bulanAktif = $activePeriod->bulan_aktif;

            $pegawaiList = Pegawai::orderBy('nama_rekening')->get();

            // Data existing HANYA untuk bulan aktif ini (bukan gelondongan 3 bulan),
            // supaya operator lihat isian yang sudah ada dan bisa mengedit ulang
            // tanpa menyentuh data bulan lain.
            $absensiExisting = AbsensiPegawai::where('tahun', $tahun)
                ->where('bulan', $bulanAktif)
                ->get()
                ->keyBy('pegawai_id');

            // ===== SUKSES =====
            Log::info('Akses form input absensi', [
                'user_id' => auth()->id(),
                'tahun' => $tahun,
                'tw' => $tw,
                'bulan_aktif' => $bulanAktif,
                'ip' => $request->ip(),
            ]);

            return view('absensi.create', [
                'pegawaiList'     => $pegawaiList,
                'absensiExisting' => $absensiExisting,
                'tahun'           => $tahun,
                'tw'              => $tw,
                'bulanAktif'      => $bulanAktif,
                'namaBulanAktif'  => $this->namaBulan[$bulanAktif],
            ]);
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

    /**
     * Simpan absensi HANYA untuk bulan aktif. Struktur input sekarang
     * absensi[pegawai_id][field] (tidak lagi dinested per bulan), sehingga
     * secara struktural tidak mungkin menyentuh/menimpa data bulan lain.
     * Divalidasi ulang di server: bulan yang dikirim harus sama persis dengan
     * bulan_aktif periode yang sedang aktif saat ini.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun'   => 'required|integer',
                'tw'      => 'required|integer|min:1|max:4',
                'bulan'   => 'required|integer|min:1|max:12',
                'absensi' => 'array',
            ]);

            $tahun = (int) $request->tahun;
            $tw    = (int) $request->tw;
            $bulan = (int) $request->bulan;

            $activePeriod = AttendancePeriod::where('is_active', true)->first();

            if (
                !$activePeriod
                || (int) $activePeriod->bulan_aktif !== $bulan
                || $activePeriod->tw_number !== $tw
                || (int) $activePeriod->tahun !== $tahun
            ) {
                return back()->with(
                    'swal_error',
                    'Bulan ini sudah tidak aktif atau tidak sesuai periode berjalan. Silakan muat ulang halaman.'
                );
            }

            $absensiData = $request->input('absensi', []);

            DB::transaction(function () use ($absensiData, $tahun, $tw, $bulan) {
                foreach ($absensiData as $pegawaiId => $data) {
                    AbsensiPegawai::updateOrCreate(
                        [
                            'pegawai_id' => $pegawaiId,
                            'bulan'      => $bulan,
                            'tahun'      => $tahun,
                            'tw'         => $tw,
                        ],
                        [
                            'sakit'          => (int) ($data['sakit'] ?? 0),
                            'izin'           => (int) ($data['izin'] ?? 0),
                            'ketidakhadiran' => (int) ($data['ketidakhadiran'] ?? 0),
                            'dinas_luar'     => (int) ($data['dinas_luar'] ?? 0),
                            'cuti'           => (int) ($data['cuti'] ?? 0),
                        ]
                    );
                }
            });

            return redirect()->route('absensi.index', [
                'tahun' => $tahun,
                'tw'    => $tw,
                'bulan' => $bulan,
            ])->with(
                'swal_success',
                'Absensi bulan ' . ($this->namaBulan[$bulan] ?? $bulan) . ' berhasil disimpan / diperbarui.'
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
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
            $this->authorize('viewAny', AbsensiPegawai::class);

            $activePeriod = AttendancePeriod::where('is_active', true)->first();

            if (!$activePeriod) {
                return back()->with('swal_error', 'Tidak ada periode aktif.');
            }

            $tahun = $request->tahun ?? $activePeriod->tahun;
            $tw = $request->tw ?? $activePeriod->tw_number;

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

            Log::error('Gagal export absensi pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return back()->with('swal_error', 'Gagal export data absensi.');
        }
    }

}