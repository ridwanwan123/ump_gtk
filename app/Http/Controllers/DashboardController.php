<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'set.unit']);
    }

    public function index()
    {
        $user = auth()->user();

        // Daftar jabatan
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

        // Query dasar pegawai
        $query = Pegawai::query();

        // Jika bukan superadmin, filter unit kerja
        if (! $user->hasRole('superadmin')) {
            $query->where('id_madrasah', $user->unit_kerja);
        }

        // Total pegawai
        $totalPegawai = $query->count();

        // Hitung per jabatan
        $statistikJabatan = [];
        foreach ($jabatanList as $jabatan) {
            $statistikJabatan[$jabatan] = (clone $query)
                ->where('jabatan', $jabatan)
                ->count();
        }

        // Data untuk Chart.js
        $chartLabels = array_keys($statistikJabatan);
        $chartData = array_values($statistikJabatan);

        // ===========================
        // Info Madrasah belum input absensi
        // ===========================

        $tahun = now()->year;
        $tw = (int) ceil(now()->month / 3);

        $bulanTW = match ($tw) {
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        };

        // Madrasah yang BELUM input absensi
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

        // ===========================
        // Grouping Madrasah Sudah / Belum by type
        // ===========================

        $madrasahSudah = Madrasah::whereNotIn('id', $madrasahBelumAbsensi->pluck('id'))
            ->orderBy('type')
            ->get()
            ->groupBy('type');


        $madrasahBelum = $madrasahBelumAbsensi
        ->sortBy('type') // urutkan collection
        ->groupBy('type');


        // Hitung total sudah/belum
        $totalMadrasah = Madrasah::count();
        $belumCount = $madrasahBelumAbsensi->count();
        $sudahCount = $totalMadrasah - $belumCount;
        $percentBelum = $totalMadrasah ? ($belumCount / $totalMadrasah) * 100 : 0;
        $percentSudah = 100 - $percentBelum;

        // ===========================
        // Return view
        // ===========================
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
    }
}