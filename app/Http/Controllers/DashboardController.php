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
            // Tentukan Tahun & TW Aktif
            // ===========================
            $tahun = now()->year;
            $tw = (int) ceil(now()->month / 3);

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

            $pegawaiQuery = Pegawai::query()
                ->when(!$user->hasRole('superadmin'), function ($q) use ($user) {
                    $q->where('id_madrasah', $user->unit_kerja);
                });

            $totalPegawai = (clone $pegawaiQuery)->count();

            $statistikJabatan = [];

            foreach ($jabatanList as $jabatan) {
                $statistikJabatan[$jabatan] = (clone $pegawaiQuery)
                    ->where('jabatan_ump', $jabatan)
                    ->count();
            }

            $chartLabels = array_keys($statistikJabatan);
            $chartData   = array_values($statistikJabatan);

            // ===========================
            // Query Madrasah
            // ===========================

            $madrasahQuery = Madrasah::query()
                ->when(!$user->hasRole('superadmin'), function ($q) use ($user) {
                    $q->where('id', $user->unit_kerja);
                });

            // ===========================
            // Madrasah SUDAH Absensi
            // ===========================

            $madrasahSudahAbsensi = (clone $madrasahQuery)
                ->whereHas('pegawai.absensi', function ($q) use ($tahun, $tw) {
                    $q->where('tahun', $tahun)
                    ->where('tw', $tw);
                })
                ->get();

            // ===========================
            // Madrasah BELUM Absensi
            // ===========================

            $madrasahBelumAbsensi = (clone $madrasahQuery)
                ->whereDoesntHave('pegawai.absensi', function ($q) use ($tahun, $tw) {
                    $q->where('tahun', $tahun)
                    ->where('tw', $tw);
                })
                ->get();

            // ===========================
            // Group per Jenjang
            // ===========================

            $madrasahSudah = $madrasahSudahAbsensi
                ->sortBy('type')
                ->groupBy('type');

            $madrasahBelum = $madrasahBelumAbsensi
                ->sortBy('type')
                ->groupBy('type');

            // ===========================
            // Statistik Absensi
            // ===========================

            $totalMadrasah = $madrasahQuery->count();

            $sudahCount = $madrasahSudahAbsensi->count();
            $belumCount = $madrasahBelumAbsensi->count();

            $percentSudah = $totalMadrasah
                ? ($sudahCount / $totalMadrasah) * 100
                : 0;

            $percentBelum = 100 - $percentSudah;

            // ===========================
            // Log Dashboard
            // ===========================

            Log::info('Akses dashboard', [
                'user_id' => $user->id,
                'role' => $user->roles->pluck('name'),
                'unit_kerja' => $user->unit_kerja,
                'tahun' => $tahun,
                'tw' => $tw,
                'ip' => request()->ip(),
            ]);

            // ===========================
            // Return View
            // ===========================

            return view('dashboard.index', [
                'totalPegawai'          => $totalPegawai,
                'statistikJabatan'      => $statistikJabatan,
                'chartLabels'           => $chartLabels,
                'chartData'             => $chartData,

                'madrasahSudahAbsensi'  => $madrasahSudahAbsensi,
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

            Log::error('Gagal memuat dashboard', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            abort(500, 'Terjadi kesalahan sistem.');
        }
    }
}
