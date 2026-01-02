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

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Pegawai::class);

            $query = Pegawai::with('madrasah');

            if ($request->filled('madrasah')) {
                $query->where('id_madrasah', $request->madrasah);
            }

            if ($request->filled('jabatan')) {
                $query->where('jabatan', $request->jabatan);
            }

            if ($request->filled('search')) {
                $query->where('nama_rekening', 'like', "%{$request->search}%");
            }

            $pegawais = $query->orderBy('id_madrasah')
                ->orderBy('nama_rekening')
                ->paginate(20)
                ->withQueryString();

            $jabatanList = Pegawai::select('jabatan')
                ->distinct()
                ->orderBy('jabatan')
                ->pluck('jabatan');

            $madrasahs = Madrasah::orderBy('nama_madrasah')->get();

            // ===== SUKSES =====
            Log::info('Akses halaman data pegawai', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return view('pegawai.index', compact('pegawais', 'madrasahs', 'jabatanList'));
        } catch (Throwable $e) {
            Log::error('Gagal membuka halaman pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            abort(500, 'Terjadi kesalahan sistem.');
        }
    }

    public function create()
    {
        try {
            $this->authorize('create', Pegawai::class);

            $madrasah = Madrasah::all();
            $jabatanList = Pegawai::select('jabatan')
                ->distinct()
                ->orderBy('jabatan')
                ->pluck('jabatan');

            Log::info('Akses form tambah pegawai', [
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return view('pegawai.form', [
                'pegawai' => new Pegawai(),
                'madrasah' => $madrasah,
                'jabatanList' => $jabatanList,
                'mode' => 'create'
            ]);
        } catch (Throwable $e) {
            Log::error('Gagal membuka form tambah pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            abort(500);
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create', Pegawai::class);

            $validated = $request->validate([
                'nama_rekening' => 'required|string|max:255',
                'nik' => 'required|string|max:20',
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'nama_ibu_kandung' => 'required|string|max:255',
                'pend_terakhir' => 'required|string|max:100',
                'alamat_gtk' => 'required|string',
                'jabatan' => 'required|string|max:100',
                'pegid' => 'nullable|string|max:50',
                'id_madrasah' => 'required|exists:madrasah,id',
                'npwp' => 'nullable|string|max:50',
                'nomor_hp' => 'nullable|string|max:20',
                'alamat_email' => 'nullable|email',
                'no_rek_bank_dki' => 'nullable|string|max:50',
            ]);

            DB::transaction(function () use ($validated) {
                Pegawai::create($validated);
            });

            Log::info('Pegawai berhasil ditambahkan', [
                'user_id' => auth()->id(),
                'nama' => $validated['nama_rekening'],
                'ip' => $request->ip(),
            ]);

            return redirect()->route('pegawai.index')
                ->with('swal_success', 'Pegawai berhasil ditambahkan');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menambahkan pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->with('swal_error', 'Terjadi kesalahan saat menambahkan data.');
        }
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        try {
            $this->authorize('update', $pegawai);

            $validated = $request->validate([
                'nama_rekening' => 'required|string|max:255',
                'nik' => 'required|string|max:20',
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'nama_ibu_kandung' => 'required|string|max:255',
                'pend_terakhir' => 'required|string|max:100',
                'alamat_gtk' => 'required|string',
                'jabatan' => 'required|string|max:100',
                'pegid' => 'nullable|string|max:50',
                'id_madrasah' => 'required|exists:madrasah,id',
                'npwp' => 'nullable|string|max:50',
                'nomor_hp' => 'nullable|string|max:20',
                'alamat_email' => 'nullable|email',
                'no_rek_bank_dki' => 'nullable|string|max:50',
            ]);

            DB::transaction(function () use ($pegawai, $validated) {
                $pegawai->update($validated);
            });

            Log::info('Data pegawai diperbarui', [
                'user_id' => auth()->id(),
                'pegawai_id' => $pegawai->id,
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->route('pegawai.show', $pegawai->id)
                ->with('swal_success', 'Data pegawai berhasil diperbarui');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal update pegawai', [
                'pegawai_id' => $pegawai->id,
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->with('swal_error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function destroy(Pegawai $pegawai)
    {
        try {
            $this->authorize('delete', $pegawai);

            DB::transaction(fn () => $pegawai->delete());

            Log::info('Pegawai dihapus', [
                'user_id' => auth()->id(),
                'pegawai_id' => $pegawai->id,
                'ip' => request()->ip(),
            ]);

            return redirect()
                ->route('pegawai.index')
                ->with('swal_success', 'Data pegawai berhasil dihapus');
        } catch (Throwable $e) {
            Log::error('Gagal menghapus pegawai', [
                'pegawai_id' => $pegawai->id,
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return back()->with('swal_error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function export()
    {
        try {
            $this->authorize('viewAny', Pegawai::class);

            Log::info('Export data pegawai', [
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return Excel::download(new PegawaiExport, 'pegawai.xlsx');
        } catch (Throwable $e) {
            Log::error('Gagal export pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return back()->with('swal_error', 'Gagal export data pegawai.');
        }
    }
}
