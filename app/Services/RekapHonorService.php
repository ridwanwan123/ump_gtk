<?php

namespace App\Services;

use App\Models\AbsensiPegawai;

class RekapHonorService
{
    public function hitung($pegawai, $bulanDipilih, $tahun, $honorPerBulan)
    {
        $absensi = AbsensiPegawai::where('pegawai_id', $pegawai->id)
            ->where('tahun', $tahun)
            ->whereIn('bulan', $bulanDipilih)
            ->get()
            ->keyBy('bulan');

        $totalS = 0;
        $totalI = 0;
        $totalTK = 0;
        $totalDL = 0;
        $totalC = 0;

        foreach ($bulanDipilih as $bulan) {
            $a = $absensi[$bulan] ?? null;

            $totalS += $a->sakit ?? 0;
            $totalI += $a->izin ?? 0;
            $totalTK += $a->ketidakhadiran ?? 0;
            $totalDL += $a->dinas_luar ?? 0;
            $totalC += $a->cuti ?? 0;
        }

        // ======================
        // BASIC VALUE
        // ======================
        $banyakBulan = count($bulanDipilih);
        $jumlahKotor = $honorPerBulan * $banyakBulan;

        // ======================
        // KEHADIRAN
        // ======================
        $totalTidakHadir = $totalS + $totalI + $totalTK + $totalC;

        $totalHariKerja = $banyakBulan * 22;

        $persenKehadiran = $totalHariKerja > 0
            ? (($totalHariKerja - $totalTidakHadir) / $totalHariKerja) * 100
            : 0;

        // ======================
        // POTONGAN (FIX MODEL EXCEL)
        // ======================

        // langsung dari HONOR BULAN (bukan per hari)
        $potonganS  = 0;
        $potonganI  = $totalI * 0.025 * $honorPerBulan;
        $potonganTK = $totalTK * 0.05 * $honorPerBulan;

        $totalPotongan = $potonganS + $potonganI + $potonganTK;

        // ======================
        // FINAL
        // ======================
        $setelahPotongan = $jumlahKotor - $totalPotongan;
        $pph = 0;
        $jumlahBersih = $setelahPotongan - $pph;

        return [
            'pegawai_id' => $pegawai->id,
            'nama' => $pegawai->nama_rekening,

            'banyak_bulan' => $banyakBulan,
            'persen_kehadiran' => round($persenKehadiran, 2),

            'total_s' => $totalS,
            'total_i' => $totalI,
            'total_tk' => $totalTK,
            'total_dl' => $totalDL,
            'total_c' => $totalC,

            'jumlah_honor_per_bulan' => $honorPerBulan,
            'jumlah_kotor' => $jumlahKotor,

            'potongan_s' => $potonganS,
            'potongan_i' => $potonganI,
            'potongan_tk' => $potonganTK,
            'total_potongan' => $totalPotongan,

            'setelah_potongan' => $setelahPotongan,
            'pph' => $pph,
            'jumlah_bersih' => $jumlahBersih,
        ];
    }
}