<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use Illuminate\Http\Request;
use App\Exports\PegawaiExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use Throwable;

class PengusulanPegawaiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Pegawai::class);

            $query = Pegawai::with('madrasah');

            if ($request->filled('madrasah')) {
                $query->where('id_madrasah', $request->madrasah);
            }

            if ($request->filled('jabatan_ump')) {
                $query->where('jabatan_ump', $request->jabatan_ump);
            }

            if ($request->filled('search')) {
                $query->where('nama_rekening', 'like', "%{$request->search}%");
            }

            $pegawais = $query->orderBy('id_madrasah')
                ->orderBy('nama_rekening')
                ->paginate(20)
                ->withQueryString();

            $jabatanList = Pegawai::select('jabatan_ump')
                ->distinct()
                ->orderBy('jabatan_ump')
                ->pluck('jabatan_ump');

            $madrasahs = Madrasah::orderBy('nama_madrasah')->get();

            // ===== SUKSES =====
            Log::info('Akses halaman data pegawai', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return view('usulan.index', compact('pegawais', 'madrasahs', 'jabatanList'));
        } catch (Throwable $e) {
            Log::error('Gagal membuka halaman pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            abort(500, 'Terjadi kesalahan sistem.');
        }
    }
}
