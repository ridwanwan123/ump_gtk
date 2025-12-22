<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
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

        // Query dasar
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

        // Siapkan data untuk Chart.js
        $chartLabels = array_keys($statistikJabatan);
        $chartData = array_values($statistikJabatan);

        return view('dashboard.index', [
            'totalPegawai'       => $totalPegawai,
            'statistikJabatan'   => $statistikJabatan,
            'chartLabels'        => $chartLabels,
            'chartData'          => $chartData
        ]);
    }
}
