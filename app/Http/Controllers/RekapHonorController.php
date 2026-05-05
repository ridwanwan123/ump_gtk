<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Madrasah;
use Illuminate\Http\Request;
use App\Services\RekapHonorService;
use App\Exports\RekapHonorExport;
use Maatwebsite\Excel\Facades\Excel;

class RekapHonorController extends Controller
{
    public function index(Request $request, RekapHonorService $service)
    {
        $madrasahs = Madrasah::all();
        $jabatanList = Pegawai::select('jabatan_ump')->distinct()->pluck('jabatan_ump');

        $data = [];

        // 🔥 HANYA JALAN KALAU FILTER DIISI
        if ($request->has(['bulan', 'tahun', 'honor'])) {

            $query = Pegawai::with('madrasah')
                    ->join('madrasah', 'pegawai.id_madrasah', '=', 'madrasah.id')
                    ->orderBy('madrasah.nama_madrasah', 'asc')
                    ->orderBy('pegawai.nama_rekening', 'asc')
                    ->select('pegawai.*');

            // Filter madrasah
            if ($request->madrasah) {
                $query->where('id_madrasah', $request->madrasah);
            }

            // Filter jabatan
            if ($request->jabatan_ump) {
                $query->where('jabatan_ump', $request->jabatan_ump);
            }

            $pegawaiList = $query->get();

            foreach ($pegawaiList as $pegawai) {
                $data[] = $service->hitung(
                    $pegawai,
                    $request->bulan,
                    $request->tahun,
                    $request->honor
                );
            }
        }

        return view('rekap-honor.index', compact(
            'madrasahs',
            'jabatanList',
            'data'
        ));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new RekapHonorExport(
                $request->bulan,
                $request->tahun,
                $request->honor
            ),
            'rekap-honor.xlsx'
        );
    }
}
