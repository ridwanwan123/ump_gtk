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
use Illuminate\Validation\Rule;
use Throwable;

class PengusulanPegawaiController extends Controller
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
            | DATA USULAN (tanpa global scope 'aktif')
            |--------------------------------------------------------------------------
            */
            $pegawaiUsulan = Pegawai::withoutGlobalScope('aktif')
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
                ->whereIn('status_pegawai', [Pegawai::USULAN, Pegawai::DITOLAK])
                ->orderByRaw('status_pegawai = ? desc', [Pegawai::USULAN])
                ->orderBy('id_madrasah')
                ->orderBy('nama_rekening')
                ->paginate(10, ['*'], 'usulan_page')
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

            Log::info('Akses halaman usulan pegawai', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return view('usulan.index', compact(
                'pegawaiUsulan',
                'madrasahs',
                'jabatanList'
            ));

        } catch (Throwable $e) {

            Log::error('Gagal membuka halaman usulan pegawai', [
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
            $user = auth()->user();
            // kasus operator
        if ($user->unit_kerja) {
            $madrasah = Madrasah::find($user->unit_kerja); // null-safe
            $isReadonly = true; // readonly input di Blade
        } else {
            // kasus superadmin
            $madrasah = Madrasah::orderBy('nama_madrasah')->get();
            $isReadonly = false; // pakai select input
        }

            $jabatanUMPList = [
                'GURU',
                'TENAGA ADMINISTRASI',
                'TENAGA KEAMANAN',
                'TENAGA KEBERSIHAN',
                'TENAGA LABORATORIUM',
                'TENAGA PENGELOLA ASRAMA',
                'TENAGA PERPUSTAKAAN',
            ];

            Log::info('Akses form tambah pegawai', [
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return view('usulan.usulan', [
                'pegawai' => new Pegawai(),
                'madrasah' => $madrasah,
                'isReadonly' => $isReadonly,
                'jabatanUMPList' => $jabatanUMPList
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

    public function show($id)
    {
        $pegawai = Pegawai::withoutGlobalScope('aktif')
            ->with('madrasah')
            ->findOrFail($id);

        return view('usulan.show', compact('pegawai'));
    }

    public function store(Request $request) //pengajuan pegawai baru
    {
        try {
            $validated = $request->validate([
                'nama_simpatika'    => 'required|string|max:255',
                'nama_rekening'     => 'required|string|max:255',
                'jabatan_ump'       => 'required|string|max:100',
                'jabatan_dinas'     => 'required|string|max:100',
                'status_asn'        => 'required|string|max:50',
                'no_rek_bank_dki'   => 'required|digits:11',
                'id_madrasah'       => 'required|exists:madrasah,id',
                'npsn_tempat_tugas' => 'required|string|max:20',
                'nik'               => [
                    'required',
                    'digits:16',
                    // NIK dari usulan yang ditolak boleh diajukan ulang
                    Rule::unique('pegawai', 'nik')->where(fn($q) =>
                        $q->where('status_pegawai', '!=', Pegawai::DITOLAK)
                    ),
                ],
                'pegid'             => 'required|digits:14',
                'tempat_lahir'      => 'required|string|max:100',
                'tanggal_lahir'     => 'required|date',
                'nama_ibu_kandung'  => 'required|string|max:255',
                'agama'             => 'required|string|max:50',
                'pend_terakhir'     => 'required|string|max:100',
                'npwp'              => 'required|digits_between:15,16',
                'nomor_hp'          => 'required|string|max:20',
                'alamat_email'      => 'required|email',
                'alamat_gtk'        => 'required|string',
                'alamat_sesuai_ktp' => 'required|string',
                'link_drive_foto_ktp' => 'required|url|max:255',
                'link_drive_emis40' => 'required|url|max:255',
                'link_drive_emis_gtk' => 'required|url|max:255',
                'status_pegawai'    => 'required|string|max:50',
                'dapodik'           => 'required|string|max:255',
            ],
            [
                'nik.required' => 'NIK wajib diisi.',
                'nik.digits'   => 'NIK harus terdiri dari 16 digit angka.',
                'nik.unique'   => 'NIK sudah terdaftar di sistem.',
            ]
            );

            DB::transaction(function () use ($validated) {
                $validated['status_pegawai'] = Pegawai::USULAN;
                $validated['alasan_ditolak'] = null;

                // usulan ulang dari data yang pernah ditolak -> perbarui data lama
                $ditolak = Pegawai::withoutGlobalScopes()
                    ->where('nik', $validated['nik'])
                    ->where('status_pegawai', Pegawai::DITOLAK)
                    ->first();

                $ditolak ? $ditolak->update($validated) : Pegawai::create($validated);
            });

            Log::info('Pegawai berhasil ditambahkan', [
                'user_id' => auth()->id(),
                'nama'    => $validated['nama_rekening'],
                'ip'      => $request->ip(),
            ]);

            return redirect()->route('pengusulan-pegawai.index')
                ->with('swal_success', 'Pegawai berhasil ditambahkan');

        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menambahkan pegawai', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip'      => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->with('swal_error', 'Terjadi kesalahan saat menambahkan data.');
        }
    }

    public function terima_pengusulan_pegawai($id)
    {
        $pegawai = Pegawai::withoutGlobalScopes()->findOrFail($id);

        try {
            DB::transaction(function () use ($pegawai) {
                $pegawai->update([
                    'status_pegawai' => 'AKTIF' // atau konstanta kalau ada
                ]);
            });

            Log::info('Pengajuan pegawai disetujui', [
                'user_id' => auth()->id(),
                'pegawai_id' => $pegawai->id,
                'ip' => request()->ip(),
            ]);

            return redirect()
                ->route('pengusulan-pegawai.index')
                ->with('swal_success', 'Pegawai berhasil disetujui.');

        } catch (Throwable $e) {

            Log::error('Gagal menyetujui pegawai', [
                'pegawai_id' => $pegawai->id,
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return back()->with('swal_error', 'Terjadi kesalahan saat proses.');
        }
    }

    public function tolak_pengusulan_pegawai(Request $request, $id)
    {
        $request->validate([
            'alasan_ditolak' => 'required|string|max:1000',
        ], [
            'alasan_ditolak.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $pegawai = Pegawai::withoutGlobalScopes()
            ->where('status_pegawai', Pegawai::USULAN)
            ->findOrFail($id);

        try {
            DB::transaction(function () use ($pegawai, $request) {
                $pegawai->update([
                    'status_pegawai' => Pegawai::DITOLAK,
                    'alasan_ditolak' => $request->alasan_ditolak,
                ]);
            });

            Log::info('Pengajuan pegawai ditolak', [
                'user_id' => auth()->id(),
                'pegawai_id' => $pegawai->id,
                'alasan' => $request->alasan_ditolak,
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->route('pengusulan-pegawai.index')
                ->with('swal_success', 'Usulan pegawai ditolak.');

        } catch (Throwable $e) {

            Log::error('Gagal menolak usulan pegawai', [
                'pegawai_id' => $pegawai->id,
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return back()->with('swal_error', 'Terjadi kesalahan saat proses.');
        }
    }
}
