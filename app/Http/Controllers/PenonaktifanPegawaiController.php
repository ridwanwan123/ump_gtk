<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PenonaktifanPegawaiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Pegawai::class);

            /*
            |--------------------------------------------------------------------------
            | BASE QUERY (FILTER)
            |--------------------------------------------------------------------------
            */
            $baseQuery = Pegawai::query()->with('madrasah');

            if ($request->filled('madrasah')) {
                $baseQuery->where('id_madrasah', $request->madrasah);
            }

            if ($request->filled('jabatan_ump')) {
                $baseQuery->where('jabatan_ump', $request->jabatan_ump);
            }

            if ($request->filled('search')) {
                $baseQuery->where('nama_rekening', 'like', "%{$request->search}%");
            }

            /*
            |--------------------------------------------------------------------------
            | DATA AKTIF (kena global scope 'aktif')
            |--------------------------------------------------------------------------
            */
            $pegawaiAktif = (clone $baseQuery)
                ->orderBy('id_madrasah')
                ->orderBy('nama_rekening')
                ->paginate(10, ['*'], 'aktif_page')
                ->withQueryString();

            /*
            |--------------------------------------------------------------------------
            | DATA PENDING (tanpa global scope 'aktif')
            |--------------------------------------------------------------------------
            */
            $pegawaiPending = Pegawai::withoutGlobalScope('aktif')
                ->with('madrasah')
                ->when($request->filled('madrasah'), fn($q) =>
                    $q->where('id_madrasah', $request->madrasah)
                )
                ->when($request->filled('jabatan_ump'), fn($q) =>
                    $q->where('jabatan_ump', $request->jabatan_ump)
                )
                ->when($request->filled('search'), fn($q) =>
                    $q->where('nama_rekening', 'like', "%{$request->search}%")
                )
                ->where('status_pegawai', 'PENDING')
                ->orderBy('id_madrasah')
                ->orderBy('nama_rekening')
                ->paginate(10, ['*'], 'pending_page')
                ->withQueryString();

            /*
            |--------------------------------------------------------------------------
            | FILTER DATA
            |--------------------------------------------------------------------------
            */
            $jabatanList = Pegawai::select('jabatan_ump')
                ->distinct()
                ->orderBy('jabatan_ump')
                ->pluck('jabatan_ump');

            $madrasahs = Madrasah::orderBy('nama_madrasah')->get();

            /*
            |--------------------------------------------------------------------------
            | LOG
            |--------------------------------------------------------------------------
            */
            Log::info('Akses halaman penonaktifan pegawai', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return view('penonaktifan.index', compact(
                'pegawaiAktif',
                'pegawaiPending',
                'madrasahs',
                'jabatanList'
            ));
        } catch (Throwable $e) {
            Log::error('Gagal membuka halaman penonaktifan pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            abort(500, 'Terjadi kesalahan sistem.');
        }
    }
}