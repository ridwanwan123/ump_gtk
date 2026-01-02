<?php
namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use Illuminate\Http\Request;
use App\Exports\PegawaiExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Pegawai::class);

        $query = Pegawai::with('madrasah'); // gunakan eager loading untuk nama madrasah

        // Filter Madrasah
        if ($request->filled('madrasah')) {
            $query->where('id_madrasah', $request->madrasah);
        }

        // Filter Jabatan
        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }

        // Filter Nama Pegawai
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_rekening', 'like', "%{$search}%");
        }

        $pegawais = $query->orderBy('id_madrasah', 'asc')
                        ->orderBy('nama_rekening', 'asc')
                        ->paginate(20)
                        ->withQueryString();

        $jabatanList = Pegawai::select('jabatan')
                ->distinct()
                ->orderBy('jabatan')
                ->pluck('jabatan');

        $madrasahs = Madrasah::orderBy('nama_madrasah')->get(); // untuk select box

        return view('pegawai.index', compact('pegawais', 'madrasahs', 'jabatanList'));
    }

    public function create()
    {
        $this->authorize('create', Pegawai::class);

        $madrasah = Madrasah::all();

        $jabatanList = Pegawai::select('jabatan')
                ->distinct()
                ->orderBy('jabatan')
                ->pluck('jabatan');

        return view('pegawai.form', [
            'pegawai' => new Pegawai(),
            'madrasah' => $madrasah,
            'jabatanList' => $jabatanList,
            'mode' => 'create'
        ]);
    }

    public function store(Request $request)
    {
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

        try {
            DB::beginTransaction();

            Pegawai::create($validated);

            DB::commit();

            return redirect()->route('pegawai.index')
                            ->with('swal_success', 'Pegawai berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menambahkan pegawai: ".$e->getMessage());

            return redirect()->back()
                            ->withInput()
                            ->withErrors(['error' => 'Terjadi kesalahan saat menambahkan data.']);
        }
    }

    public function show(Pegawai $pegawai)
    {
        $this->authorize('view', $pegawai);

        return view('pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        $madrasah = Madrasah::all();
        $jabatanList = Pegawai::select('jabatan')
                ->distinct()
                ->orderBy('jabatan')
                ->pluck('jabatan');

        return view('pegawai.form', [
            'pegawai' => $pegawai,
            'madrasah' => $madrasah,
            'jabatanList' => $jabatanList,
            'mode' => 'edit'
        ]);
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
                ->with('swal_success', 'Data pegawai berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update pegawai: ".$e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
        }
    }

    public function destroy(Pegawai $pegawai)
    {
        $this->authorize('delete', $pegawai);

        try {
            DB::beginTransaction();

            $pegawai->delete();

            DB::commit();

            return redirect()
                ->route('pegawai.index')
                ->with('success', 'Data pegawai berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus pegawai: ".$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus data.']);
        }
    }

    public function export()
    {
        $this->authorize('viewAny', Pegawai::class); // atur permission

        return Excel::download(new PegawaiExport, 'pegawai.xlsx');
    }
}
