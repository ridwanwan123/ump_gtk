<?php
namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PegawaiController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Pegawai::class);

        $pegawais = Pegawai::query()
            ->leftJoin('madrasah', 'pegawai.id_madrasah', '=', 'madrasah.id')
            ->orderBy('madrasah.nama_madrasah', 'asc')
            ->orderBy('pegawai.nama_rekening', 'asc')
            ->select('pegawai.*') // WAJIB agar model Pegawai tetap utuh
            ->paginate(20);

        // return response()->json($pegawais);
        return view('pegawai.index', compact('pegawais'));
    }

    public function show(Pegawai $pegawai)
    {
        $this->authorize('view', $pegawai);

        return view('pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        $this->authorize('update', $pegawai);

        // ambil data madrasah untuk dropdown
        $madrasah = DB::table('madrasah')
            ->orderBy('nama_madrasah', 'asc')
            ->get();

        return view('pegawai.edit', compact('pegawai', 'madrasah'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
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

        try {
            DB::beginTransaction();

            $pegawai->update($validated);

            DB::commit();

            return redirect()
                ->route('pegawai.show', $pegawai->id)
                ->with('success', 'Data pegawai berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update pegawai: ".$e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
        }
    }

}
