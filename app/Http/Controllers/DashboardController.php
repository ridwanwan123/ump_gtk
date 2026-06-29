<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use App\Models\AttendancePeriod;
use Illuminate\Support\Facades\Log;
use Throwable;
use Carbon\Carbon;

class DashboardController extends Controller
{
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
            $tw = (int) str_replace('TW ', '', $activePeriod->triwulan);

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

            $pegawaiAkanPensiun = (clone $pegawaiQuery)
                ->with('madrasah')
                ->whereDate('tanggal_lahir', '<=', now()->subYears(58))
                ->orderBy('tanggal_lahir')
                ->get()
                ->map(function ($pegawai) {
                    $lahir = \Carbon\Carbon::parse($pegawai->tanggal_lahir);

                    $pegawai->usia = $lahir->age;
                    $pegawai->tanggal_pensiun = $lahir->copy()->addYears(60);
                    $pegawai->sisa_tahun = max(0, 60 - $pegawai->usia);

                    return $pegawai;
                });

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

            $madrasahSudahAbsensi = (clone $madrasahQuery)
                ->whereHas('pegawai.absensi', function ($q) use ($tahun, $tw) {
                    $q->where('tahun', $tahun)
                        ->where('tw', $tw);
                })
                ->get();

            $madrasahBelumAbsensi = (clone $madrasahQuery)
                ->whereDoesntHave('pegawai.absensi', function ($q) use ($tahun, $tw) {
                    $q->where('tahun', $tahun)
                        ->where('tw', $tw);
                })
                ->get();

            $madrasahSudah = $madrasahSudahAbsensi
                ->sortBy('type')
                ->groupBy('type');

            $madrasahBelum = $madrasahBelumAbsensi
                ->sortBy('type')
                ->groupBy('type');

            $sudahCount = $madrasahSudahAbsensi->count();
            $belumCount = $madrasahBelumAbsensi->count();

            $percentSudah = $totalMadrasah
                ? round(($sudahCount / $totalMadrasah) * 100, 2)
                : 0;

            $percentBelum = 100 - $percentSudah;

            /*
            |--------------------------------------------------------------------------
            | Hak Pembayaran
            |--------------------------------------------------------------------------
            */

            $madrasahSudahHak = (clone $madrasahQuery)
                ->whereHas('pegawai.hakPembayaranPegawai', function ($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                })
                ->get();

            $madrasahBelumHak = (clone $madrasahQuery)
                ->whereDoesntHave('pegawai.hakPembayaranPegawai', function ($q) use ($tahun) {
                    $q->where('tahun', $tahun);
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
