<?php
namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','set.unit']); // penting
        $this->middleware('role:superadmin|bendahara')->only('index');
    }

    public function index(Request $request)
    {
        // global scope sudah lakukan filter untuk bendahara
        $pegawais = Pegawai::orderBy('nama_rekening')->paginate(20);

        return view('pegawai.index', compact('pegawais'));
    }
}
