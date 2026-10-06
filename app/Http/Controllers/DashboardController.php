<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use App\Models\AttendancePeriod;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'set.unit']);
    }

    public function index()
    {
        try {
            $user = auth()->user();

            $activePeriod = AttendancePeriod::where('is_active', true)->first();

            if (!$activePeriod) {
                abort(404, 'Tidak ada periode aktif.');
            }

            $tahun = $activePeriod->tahun;
            $tw = $activePeriod->tw_number;
            $bulanAktif = $activePeriod->bulan_aktif;
            $namaBulanAktif = $bulanAktif ? ($this->namaBulan[$bulanAktif] ?? null) : null;

            /*
            |--------------------------------------------------------------------------
            | Query Pegawai
            |--------------------------------------------------------------------------
            */

            $pegawaiQuery = Pegawai::query()
                ->when(!$user->hasRole('superadmin'), function ($q) use ($user) {
                    $q->where('id_madrasah', $user->unit_kerja);
                });

            $totalPegawai = (clone $pegawaiQuery)->count();

            /*
            |--------------------------------------------------------------------------
            | Statistik Jabatan
            |--------------------------------------------------------------------------
            */

            $statistikJabatan = (clone $pegawaiQuery)
                ->selectRaw('jabatan_ump, COUNT(*) as total')
                ->groupBy('jabatan_ump')
                ->orderByDesc('total')
                ->orderBy('jabatan_ump')
                ->pluck('total', 'jabatan_ump');

            $chartLabels = $statistikJabatan->keys();
            $chartData   = $statistikJabatan->values();

            /*
            |--------------------------------------------------------------------------
            | Statistik Pendidikan
            |--------------------------------------------------------------------------
            */

            $statistikPendidikan = (clone $pegawaiQuery)
                ->selectRaw('pend_terakhir, COUNT(*) as total')
                ->groupBy('pend_terakhir')
                ->orderBy('pend_terakhir')
                ->pluck('total', 'pend_terakhir');

            $pendidikanLabels = $statistikPendidikan->keys();
            $pendidikanData = $statistikPendidikan
                ->values()
                ->map('intval')
                ->values();

            /*
            |--------------------------------------------------------------------------
            | Pegawai per Jenjang
            |--------------------------------------------------------------------------
            */

            $pegawaiPerJenjang = (clone $pegawaiQuery)
                ->join('madrasah', 'pegawai.id_madrasah', '=', 'madrasah.id')
                ->selectRaw('madrasah.type, COUNT(*) as total')
                ->groupBy('madrasah.type')
                ->pluck('total', 'type');

            $jenjangLabels = $pegawaiPerJenjang->keys();
            $jenjangData   = $pegawaiPerJenjang->values();

            /*
            |--------------------------------------------------------------------------
            | Pegawai per Madrasah
            |--------------------------------------------------------------------------
            */

            $pegawaiPerMadrasah = Madrasah::query()
                ->when(!$user->hasRole('superadmin'), function ($q) use ($user) {
                    $q->where('id', $user->unit_kerja);
                })
                ->withCount('pegawai')
                ->orderByDesc('pegawai_count')
                ->limit(10)
                ->get();

            $madrasahLabels = $pegawaiPerMadrasah->pluck('nama_madrasah');
            $madrasahData   = $pegawaiPerMadrasah->pluck('pegawai_count');

            /*
            |--------------------------------------------------------------------------
            | Pegawai Akan Pensiun (58 Tahun ke Atas)
            |--------------------------------------------------------------------------
            */

            $usiaPensiunMap = function ($pegawai) {
                $lahir = \Carbon\Carbon::parse($pegawai->tanggal_lahir);

                $usiaPensiun = Pegawai::usiaPensiun($pegawai->jabatan_dinas);

                $pegawai->usia = $lahir->age;
                $pegawai->usia_pensiun = $usiaPensiun;
                $pegawai->tanggal_pensiun = $lahir->copy()->addYears($usiaPensiun);
                $pegawai->sisa_tahun = max(0, $usiaPensiun - $pegawai->usia);

                return $pegawai;
            };

            // Disaring & diurutkan di database (bukan memuat semua pegawai ke memori):
            // tampil mulai 2 tahun sebelum usia pensiun -> PENDIDIK usia >= 58, lainnya >= 56.
            $batasLahirPendidik = now()->subYears(58)->toDateString();
            $batasLahirLainnya  = now()->subYears(56)->toDateString();

            $pegawaiAkanPensiun = (clone $pegawaiQuery)
                ->with('madrasah')
                ->where(function ($q) use ($batasLahirPendidik, $batasLahirLainnya) {
                    $q->where(function ($q) use ($batasLahirPendidik) {
                        $q->where('jabatan_dinas', 'PENDIDIK')
                            ->whereDate('tanggal_lahir', '<=', $batasLahirPendidik);
                    })->orWhere(function ($q) use ($batasLahirLainnya) {
                        $q->where(function ($q) {
                            $q->where('jabatan_dinas', '!=', 'PENDIDIK')
                                ->orWhereNull('jabatan_dinas');
                        })->whereDate('tanggal_lahir', '<=', $batasLahirLainnya);
                    });
                })
                // urut berdasarkan tanggal pensiun terdekat
                ->orderByRaw("DATE_ADD(tanggal_lahir, INTERVAL (CASE WHEN jabatan_dinas = 'PENDIDIK' THEN 60 ELSE 58 END) YEAR)")
                ->paginate(10)
                ->withQueryString()
                ->through($usiaPensiunMap);

            /*
            |--------------------------------------------------------------------------
            | Query Madrasah
            |--------------------------------------------------------------------------
            */

            $madrasahQuery = Madrasah::query()
                ->when(!$user->hasRole('superadmin'), function ($q) use ($user) {
                    $q->where('id', $user->unit_kerja);
                });

            $totalMadrasah = (clone $madrasahQuery)->count();

            /*
            |--------------------------------------------------------------------------
            | Absensi
            |--------------------------------------------------------------------------
            */

            // Kalau superadmin belum menentukan bulan_aktif untuk periode ini,
            // tidak ada "bulan yang sedang berjalan" untuk dibandingkan ->
            // anggap semua madrasah masih "belum mengisi" (bukan whereNull('bulan')
            // yang salah makna kalau bulan_aktif kosong).
            // Status input hanya dihitung untuk madrasah yang punya pegawai (aktif),
            // madrasah tanpa pegawai tidak perlu mengisi absensi / hak pembayaran.
            $madrasahBerpegawaiQuery = (clone $madrasahQuery)->has('pegawai');

            $totalMadrasahBerpegawai = (clone $madrasahBerpegawaiQuery)->count();

            if (!$bulanAktif) {
                $madrasahSudahAbsensi = (clone $madrasahBerpegawaiQuery)->whereRaw('1 = 0')->get();
                $madrasahBelumAbsensi = (clone $madrasahBerpegawaiQuery)->get();
            } else {
                $madrasahSudahAbsensi = (clone $madrasahBerpegawaiQuery)
                    ->whereHas('pegawai.absensi', function ($q) use ($tahun, $tw, $bulanAktif) {
                        $q->where('tahun', $tahun)
                            ->where('tw', $tw)
                            ->where('bulan', $bulanAktif);
                    })
                    ->get();

                $madrasahBelumAbsensi = (clone $madrasahBerpegawaiQuery)
                    ->whereDoesntHave('pegawai.absensi', function ($q) use ($tahun, $tw, $bulanAktif) {
                        $q->where('tahun', $tahun)
                            ->where('tw', $tw)
                            ->where('bulan', $bulanAktif);
                    })
                    ->get();
            }

            $madrasahSudah = $madrasahSudahAbsensi
                ->sortBy('type')
                ->groupBy('type');

            $madrasahBelum = $madrasahBelumAbsensi
                ->sortBy('type')
                ->groupBy('type');

            $sudahCount = $madrasahSudahAbsensi->count();
            $belumCount = $madrasahBelumAbsensi->count();

            $percentSudah = $totalMadrasahBerpegawai
                ? round(($sudahCount / $totalMadrasahBerpegawai) * 100, 2)
                : 0;

            $percentBelum = 100 - $percentSudah;

            /*
            |--------------------------------------------------------------------------
            | Hak Pembayaran
            |--------------------------------------------------------------------------
            */

            // Sama seperti menu Hak Pembayaran: dihitung per triwulan periode aktif
            // (tahun + bulan-bulan dalam TW), bukan setahun penuh.
            $bulanTw = $activePeriod->bulan_list;

            $madrasahSudahHak = (clone $madrasahBerpegawaiQuery)
                ->whereHas('pegawai.hakPembayaranPegawai', function ($q) use ($tahun, $bulanTw) {
                    $q->where('tahun', $tahun)
                        ->whereIn('bulan', $bulanTw);
                })
                ->get();

            $madrasahBelumHak = (clone $madrasahBerpegawaiQuery)
                ->whereDoesntHave('pegawai.hakPembayaranPegawai', function ($q) use ($tahun, $bulanTw) {
                    $q->where('tahun', $tahun)
                        ->whereIn('bulan', $bulanTw);
                })
                ->get();

            $madrasahSudahHakGroup = $madrasahSudahHak
                ->sortBy('type')
                ->groupBy('type');

            $madrasahBelumHakGroup = $madrasahBelumHak
                ->sortBy('type')
                ->groupBy('type');

            $sudahHakCount = $madrasahSudahHak->count();
            $belumHakCount = $madrasahBelumHak->count();

            /*
            |--------------------------------------------------------------------------
            | Return View
            |--------------------------------------------------------------------------
            */

            
            return view('dashboard.index', [
                'tahun' => $tahun,
                'tw' => $tw,
                'bulanAktif' => $bulanAktif,
                'namaBulanAktif' => $namaBulanAktif,

                'totalPegawai' => $totalPegawai,
                'totalMadrasah' => $totalMadrasah,

                //
                'jenjangLabels' => $jenjangLabels,
                'jenjangData'   => $jenjangData,

                //
                'madrasahLabels' => $madrasahLabels,
                'madrasahData'   => $madrasahData,

                // Statistik Jabatan
                'statistikJabatan' => $statistikJabatan,
                'chartLabels' => $chartLabels,
                'chartData' => $chartData,

                // // Statistik Pendidikan
                'statistikPendidikan' => $statistikPendidikan,
                'pendidikanLabels' => $pendidikanLabels,
                'pendidikanData' => $pendidikanData,

                // // Pegawai
                'pegawaiAkanPensiun' => $pegawaiAkanPensiun,

                // Absensi
                'madrasahSudahAbsensi' => $madrasahSudahAbsensi,
                'madrasahBelumAbsensi' => $madrasahBelumAbsensi,
                'madrasahSudah' => $madrasahSudah,
                'madrasahBelum' => $madrasahBelum,
                'sudahCount' => $sudahCount,
                'belumCount' => $belumCount,
                'percentSudah' => $percentSudah,
                'percentBelum' => $percentBelum,

                // Hak Pembayaran
                'madrasahSudahHak' => $madrasahSudahHak,
                'madrasahBelumHak' => $madrasahBelumHak,
                'madrasahSudahHakGroup' => $madrasahSudahHakGroup,
                'madrasahBelumHakGroup' => $madrasahBelumHakGroup,
                'sudahHakCount' => $sudahHakCount,
                'belumHakCount' => $belumHakCount,
            ]);
        } catch (HttpExceptionInterface $e) {
            // abort(404/403) di atas jangan ikut diubah jadi 500
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal memuat dashboard', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            abort(500, 'Terjadi kesalahan sistem.');
        }
    }
}