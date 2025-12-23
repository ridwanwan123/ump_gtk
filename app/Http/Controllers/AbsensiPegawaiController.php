<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiPegawai;


class AbsensiPegawaiController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', AbsensiPegawai::class);

        $data = AbsensiPegawai::with('pegawai')->paginate(15);
        return view('absensi.index', compact('data'));
    }

    public function store(Request $request)
    {
        $absensi = new AbsensiPegawai($request->all());

        $this->authorize('create', $absensi);

        $absensi->save();

        return back()->with('success', 'Absensi berhasil disimpan');
    }
}
