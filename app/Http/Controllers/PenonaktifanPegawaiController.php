<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PenonaktifanPegawaiController extends Controller
{
    public function index(Request $request)
    {
        try {
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
                ->where('status_pegawai', Pegawai::PROSES_NON_AKTIF)
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

    public function show($id)
    {
        $pegawai = Pegawai::withoutGlobalScope('aktif')
            ->with('madrasah')
            ->findOrFail($id);

        return view('penonaktifan.show', compact('pegawai'));
    }

    public function pengajuan_nonaktif_pegawai(Request $request, Pegawai $pegawai)
    {
        $this->authorize('proses', $pegawai);

        // VALIDASI
        $request->validate([
            'alasan_mengundurkan_diri' => 'required|string',
            'tgl_nonaktif' => 'required|date'
        ]);

        try {
            DB::transaction(function () use ($pegawai, $request) {
                $pegawai->update([
                    'status_pegawai' => Pegawai::PROSES_NON_AKTIF,
                    'alasan_mengundurkan_diri' => $request->alasan_mengundurkan_diri,
                    'tgl_nonaktif' => $request->tgl_nonaktif
                ]);
            });

            Log::info('Pengajuan penonaktifan pegawai diajukan', [
                'user_id' => auth()->id(),
                'pegawai_id' => $pegawai->id,
                'alasan' => $request->alasan_mengundurkan_diri,
                'tgl_nonaktif' => $request->tgl_nonaktif,
                'ip' => request()->ip(),
            ]);

            return redirect()
                ->route('penonaktifan-pegawai.index')
                ->with('swal_success', 'Pengajuan penonaktifan pegawai berhasil.');

        } catch (Throwable $e) {
            Log::error('Gagal mengajukan penonaktifan pegawai', [
                'pegawai_id' => $pegawai->id,
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return back()->with('swal_error', 'Terjadi kesalahan saat proses.');
        }
    }
    
    public function terima_nonaktif_pegawai($id)
    {
        $pegawai = Pegawai::withoutGlobalScopes()->findOrFail($id);

        try {
            DB::transaction(function () use ($pegawai) {
                $pegawai->update([
                'status_pegawai' => Pegawai::NON_AKTIF
                ]);
            });

            Log::info('Pengajuan penonaktifan pegawai diterima', [
                'user_id' => auth()->id(),
                'pegawai_id' => $pegawai->id,
                'ip' => request()->ip(),
            ]);

            return redirect()
                ->route('penonaktifan-pegawai.index')
                ->with('swal_success', 'Penonaktifan pegawai berhasil diterima.');

        } catch (Throwable $e) {
            
            Log::error('Gagal menonaktifkan pegawai dari penonaktifan', [
                'pegawai_id' => $pegawai->id,
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return back()->with('swal_error', 'Terjadi kesalahan saat proses.');
        }
    }

    public function tolak_nonaktif_pegawai($id)
    {
        $pegawai = Pegawai::withoutGlobalScopes()->findOrFail($id);

        try {
            DB::transaction(function () use ($pegawai) {
                $pegawai->update([
                    'status_pegawai' => Pegawai::AKTIF
                ]);
            });

            return redirect()
                ->route('penonaktifan-pegawai.index')
                ->with('swal_success', 'Pengajuan penonaktifan ditolak.');

        } catch (\Throwable $e) {
            return back()->with('swal_error', 'Terjadi kesalahan saat proses.');
        }
    }
}