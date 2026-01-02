<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

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

            // ===========================
            // Statistik Pegawai
            // ===========================

            $jabatanList = [
                'Guru',
                'Kepala Pengelola Asrama',
                'Tenaga Administrasi',
                'Tenaga Keamanan',
                'Tenaga Kebersihan',
                'Tenaga Laboratorium',
                'Tenaga Pengelola Asrama',
                'Tenaga Perpustakaan',
            ];

            $query = Pegawai::query();

            if (! $user->hasRole('superadmin')) {
                $query->where('id_madrasah', $user->unit_kerja);
            }

            $totalPegawai = $query->count();

            $statistikJabatan = [];
            foreach ($jabatanList as $jabatan) {
                $statistikJabatan[$jabatan] = (clone $query)
                    ->where('jabatan', $jabatan)
                    ->count();
            }

            $chartLabels = array_keys($statistikJabatan);
            $chartData = array_values($statistikJabatan);

            // ===========================
            // Info Absensi Madrasah
            // ===========================

            $tahun = now()->year;
            $tw = (int) ceil(now()->month / 3);

            $bulanTW = match ($tw) {
                1 => [1, 2, 3],
                2 => [4, 5, 6],
                3 => [7, 8, 9],
                4 => [10, 11, 12],
            };

            $madrasahBelumAbsensi = Madrasah::whereHas('pegawai')
                ->whereDoesntHave('pegawai.absensi', function ($q) use ($tahun, $bulanTW) {
                    $q->where('tahun', $tahun)
                      ->whereIn('bulan', $bulanTW);
                })
                ->when(! $user->hasRole('superadmin'), function ($q) use ($user) {
                    $q->where('id', $user->unit_kerja);
                })
                ->orderBy('nama_madrasah')
                ->get();

            $madrasahSudah = Madrasah::whereNotIn('id', $madrasahBelumAbsensi->pluck('id'))
                ->orderBy('type')
                ->get()
                ->groupBy('type');

            $madrasahBelum = $madrasahBelumAbsensi
                ->sortBy('type')
                ->groupBy('type');

            $totalMadrasah = Madrasah::count();
            $belumCount = $madrasahBelumAbsensi->count();
            $sudahCount = $totalMadrasah - $belumCount;

            $percentBelum = $totalMadrasah
                ? ($belumCount / $totalMadrasah) * 100
                : 0;

            $percentSudah = 100 - $percentBelum;

            // ===== SUKSES =====
            Log::info('Akses dashboard', [
                'user_id' => $user->id,
                'role' => $user->roles->pluck('name'),
                'unit_kerja' => $user->unit_kerja,
                'tahun' => $tahun,
                'tw' => $tw,
                'ip' => request()->ip(),
            ]);

            return view('dashboard.index', [
                'totalPegawai'          => $totalPegawai,
                'statistikJabatan'      => $statistikJabatan,
                'chartLabels'           => $chartLabels,
                'chartData'             => $chartData,
                'madrasahBelumAbsensi'  => $madrasahBelumAbsensi,
                'madrasahSudah'         => $madrasahSudah,
                'madrasahBelum'         => $madrasahBelum,
                'tw'                    => $tw,
                'tahun'                 => $tahun,
                'sudahCount'            => $sudahCount,
                'belumCount'            => $belumCount,
                'percentSudah'          => $percentSudah,
                'percentBelum'          => $percentBelum,
            ]);
        } catch (Throwable $e) {
            // ===== ERROR =====
            Log::error('Gagal memuat dashboard', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            abort(500, 'Terjadi kesalahan sistem.');
        }
    }
}
