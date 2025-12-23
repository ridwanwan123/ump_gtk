<?php
namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Pegawai::class);

        $pegawais = Pegawai::paginate(15);
        return view('pegawai.index', compact('pegawais'));
    }

    public function show(Pegawai $pegawai)
    {
        $this->authorize('view', $pegawai);

        return view('pegawai.show', compact('pegawai'));
    }
}
