<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PusatDataPegawaiExport;
use App\Exports\PusatDataPensiunExport;
use App\Http\Controllers\Controller;
use App\Models\Madrasah;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PusatDataController extends Controller
{
    /**
     * Pilihan filter status. "NON AKTIF" mencakup juga penulisan lama "NON_AKTIF"
     * yang masih ada di database.
     */
    private const STATUS = [
        Pegawai::AKTIF            => [Pegawai::AKTIF],
        Pegawai::PROSES_NON_AKTIF => [Pegawai::PROSES_NON_AKTIF],
        Pegawai::NON_AKTIF        => [Pegawai::NON_AKTIF, 'NON_AKTIF'],
        Pegawai::PROSES_USULAN    => [Pegawai::PROSES_USULAN],
        Pegawai::USULAN           => [Pegawai::USULAN],
        Pegawai::DITOLAK          => [Pegawai::DITOLAK],
    ];

    public function index(Request $request)
    {
        $this->validateFilter($request);

        $query = $this->pegawaiQuery($request);

        return view('pusat_data.index', [
            'totalPegawai' => (clone $query)->count(),
            'pegawai'      => $query->paginate(10)->withQueryString(),

            // opsi filter
            'statusList'       => array_keys(self::STATUS),
            'madrasahList'     => Madrasah::orderBy('nama_madrasah')->get(['id', 'nama_madrasah']),
            'jenjangList'      => Madrasah::whereNotNull('type')->distinct()->orderBy('type')->pluck('type'),
            'jabatanUmpList'   => $this->distinctPegawai('jabatan_ump'),
            'jabatanDinasList' => $this->distinctPegawai('jabatan_dinas'),
            'pendidikanList'   => $this->distinctPegawai('pend_terakhir'),
        ]);
    }

    public function exportPegawai(Request $request)
    {
        $this->validateFilter($request);

        $query = $this->pegawaiQuery($request);
        $total = (clone $query)->count();

        if ($total === 0) {
            return back()->with('swal_error', 'Tidak ada data pegawai yang sesuai filter.');
        }

        Log::info('Export pusat data pegawai', [
            'user_id' => auth()->id(),
            'filter'  => $request->only(['status', 'madrasah', 'jenjang', 'jabatan_ump', 'jabatan_dinas', 'pend_terakhir']),
            'total'   => $total,
            'ip'      => $request->ip(),
        ]);

        return Excel::download(
            new PusatDataPegawaiExport($query),
            'Pusat Data Pegawai ' . now()->format('Y-m-d His') . '.xlsx'
        );
    }

    public function exportPensiun(Request $request)
    {
        $validated = $request->validate([
            'range_tahun'   => 'required|integer|min:1|max:40',
            'madrasah'      => 'nullable|integer',
            'jenjang'       => 'nullable|array',
            'jenjang.*'     => 'string',
            'jabatan_dinas' => 'nullable|array',
            'jabatan_dinas.*' => 'string',
        ], [
            'range_tahun.required' => 'Rentang tahun wajib diisi.',
            'range_tahun.min'      => 'Rentang tahun minimal 1.',
            'range_tahun.max'      => 'Rentang tahun maksimal 40.',
        ]);

        $range = (int) $validated['range_tahun'];

        // Tanggal pensiun = tanggal lahir + usia pensiun (PENDIDIK 60, lainnya 58)
        $tanggalPensiun = "DATE_ADD(pegawai.tanggal_lahir, INTERVAL (CASE WHEN pegawai.jabatan_dinas = 'PENDIDIK' THEN "
            . Pegawai::USIA_PENSIUN_PENDIDIK . ' ELSE ' . Pegawai::USIA_PENSIUN_LAINNYA . ' END) YEAR)';

        // Hanya pegawai AKTIF (global scope 'aktif' tetap berlaku)
        $query = $this->baseQuery(Pegawai::query(), $request)
            ->whereNotNull('pegawai.tanggal_lahir')
            // Batas dihitung sampai akhir tahun: hari ini 6 Okt 2026 + 3 tahun -> s.d. 31 Des 2029
            ->whereRaw("$tanggalPensiun <= ?", [now()->addYears($range)->endOfYear()->toDateString()])
            ->when(!$request->boolean('termasuk_lewat'), function (Builder $q) use ($tanggalPensiun) {
                // tanpa centang: yang sudah melewati usia pensiun tidak ikut
                $q->whereRaw("$tanggalPensiun > ?", [now()->toDateString()]);
            })
            ->reorder()
            ->orderByRaw($tanggalPensiun)
            ->orderBy('pegawai.nama_rekening');

        $total = (clone $query)->count();

        if ($total === 0) {
            return back()->withInput()->with('swal_error', 'Tidak ada pegawai yang pensiun sampai dengan 31 Desember ' . now()->addYears($range)->year . '.');
        }

        Log::info('Export pusat data pensiun', [
            'user_id'     => auth()->id(),
            'range_tahun' => $range,
            'total'       => $total,
            'ip'          => $request->ip(),
        ]);

        return Excel::download(
            new PusatDataPensiunExport($query),
            "Data Pensiun {$range} Tahun " . now()->format('Y-m-d His') . '.xlsx'
        );
    }

    private function validateFilter(Request $request): void
    {
        $request->validate([
            'status'          => 'nullable|array',
            'status.*'        => 'string',
            'madrasah'        => 'nullable|integer',
            'jenjang'         => 'nullable|array',
            'jenjang.*'       => 'string',
            'jabatan_ump'     => 'nullable|array',
            'jabatan_ump.*'   => 'string',
            'jabatan_dinas'   => 'nullable|array',
            'jabatan_dinas.*' => 'string',
            'pend_terakhir'   => 'nullable|array',
            'pend_terakhir.*' => 'string',
        ]);
    }

    /**
     * Query pegawai semua status (tanpa global scope 'aktif'), lalu dipersempit filter.
     * Filter yang kosong berarti "semua".
     */
    private function pegawaiQuery(Request $request): Builder
    {
        $status = collect($request->input('status', []))
            ->flatMap(fn ($s) => self::STATUS[$s] ?? [])
            ->all();

        return $this->baseQuery(Pegawai::withoutGlobalScope('aktif'), $request)
            ->when($status, fn (Builder $q) => $q->whereIn('pegawai.status_pegawai', $status))
            ->when($request->input('jabatan_ump'), fn (Builder $q, $v) => $q->whereIn('pegawai.jabatan_ump', $v))
            ->when($request->input('pend_terakhir'), fn (Builder $q, $v) => $q->whereIn('pegawai.pend_terakhir', $v));
    }

    /**
     * Bagian query yang dipakai bersama export pegawai & export pensiun:
     * join madrasah (untuk urutan & filter jenjang) + filter madrasah, jenjang, jabatan dinas.
     */
    private function baseQuery(Builder $query, Request $request): Builder
    {
        return $query
            ->with('madrasah')
            ->join('madrasah', 'pegawai.id_madrasah', '=', 'madrasah.id')
            ->select('pegawai.*')
            ->when($request->input('madrasah'), fn (Builder $q, $v) => $q->where('pegawai.id_madrasah', $v))
            ->when($request->input('jenjang'), fn (Builder $q, $v) => $q->whereIn('madrasah.type', $v))
            ->when($request->input('jabatan_dinas'), fn (Builder $q, $v) => $q->whereIn('pegawai.jabatan_dinas', $v))
            ->orderBy('madrasah.nama_madrasah')
            ->orderBy('pegawai.nama_rekening');
    }

    private function distinctPegawai(string $column)
    {
        return Pegawai::withoutGlobalScope('aktif')
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column);
    }
}
