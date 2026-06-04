<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Madrasah;
use App\Models\HakPembayaranPegawai;

class HakPembayaranPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $madrasahId = $request->madrasah;
        $tw = $request->tw ?? 1;
        $tahun = $request->tahun ?? date('Y');

        // =========================
        // MASTER DATA
        // =========================
        $madrasahList = Madrasah::orderBy('nama_madrasah')->get();

        // =========================
        // BULAN DARI TW
        // =========================
        $bulan = $this->getBulanFromTW($tw);

        // =========================
        // QUERY PEGAWAI
        // =========================
        $pegawai = Pegawai::query()
            ->with('madrasah')
            ->when($madrasahId, function ($q) use ($madrasahId) {
                $q->where('id_madrasah', $madrasahId);
            })
            ->orderBy('nama_rekening')
            ->get();

        // =========================
        // HAK PEMBAYARAN (DI-LOAD SEKALIGUS)
        // =========================
        $hakRaw = HakPembayaranPegawai::where('tahun', $tahun)
            ->whereIn('bulan', $bulan)
            ->get()
            ->groupBy('pegawai_id');

        $hakMap = [];

        foreach ($hakRaw as $pegawaiId => $items) {
            foreach ($items as $item) {
                $hakMap[(int) $pegawaiId][(int) $item->bulan] = (int) $item->status_bayar;
            }
        }

        return view('hak_pegawai.index', [
            'madrasahList' => $madrasahList,
            'pegawai' => $pegawai,
            'bulan' => $bulan,
            'tw' => $tw,
            'tahun' => $tahun,
            'hakMap' => $hakMap,
        ]);
    }

    /**
     * Convert TW ke bulan
     */
    private function getBulanFromTW($tw)
    {
        return match ((int) $tw) {
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
            default => [1, 2, 3],
        };
    }
}