<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiPegawai;
use App\Models\Pegawai; // import model Pegawai

class AbsensiPegawaiController extends Controller
{
    // public function index()
    // {
    //     $this->authorize('viewAny', AbsensiPegawai::class);

    //     // Ambil semua pegawai tanpa paginate agar bisa tampil sekaligus
    //     $pegawaiList = Pegawai::orderBy('nama_rekening')->paginate(10);
    //     $tw = request()->get('tw', 1); // Ambil triwulan dari request, default 1

    //     // Kirim data pegawai ke view
    //     return view('absensi.index', compact('pegawaiList', 'tw'));
    // }

    public function index(Request $request)
    {
        $this->authorize('viewAny', AbsensiPegawai::class);
        
        $tw = $request->tw ?? ceil(now()->month / 3); // default triwulan sekarang
        $bulanPerTW = [
            1 => ['Januari','Februari','Maret'],
            2 => ['April','Mei','Juni'],
            3 => ['Juli','Agustus','September'],
            4 => ['Oktober','November','Desember'],
        ];

        $bulan = $bulanPerTW[$tw];

        $pegawaiList = Pegawai::paginate(10); // misal paginasi

        return view('absensi.index', compact('pegawaiList','bulan','tw'));
    }

    public function store(Request $request)
    {
        $absensi = new AbsensiPegawai($request->all());

        $this->authorize('create', $absensi);

        $absensi->save();

        return back()->with('success', 'Absensi berhasil disimpan');
    }

    // nanti bisa tambahkan bulkStore untuk simpan bulk absensi
}
