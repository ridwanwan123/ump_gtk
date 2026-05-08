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

        $detailBulan = [];

        foreach ($bulanDipilih as $bulan) {

            $a = $absensi[$bulan] ?? null;

            $s  = $a->sakit ?? 0;
            $i  = $a->izin ?? 0;
            $tk = $a->ketidakhadiran ?? 0;
            $dl = $a->dinas_luar ?? 0;
            $c  = $a->cuti ?? 0;

            $detailBulan[$bulan] = [
                's' => $s,
                'i' => $i,
                'tk' => $tk,
                'dl' => $dl,
                'c' => $c,
            ];

            $totalS += $s;
            $totalI += $i;
            $totalTK += $tk;
            $totalDL += $dl;
            $totalC += $c;
        }

        $banyakBulan = count($bulanDipilih);

        $jumlahKotor = $honorPerBulan * $banyakBulan;

        $totalTidakHadir = $totalS + $totalI + $totalTK + $totalC;

        $totalHariKerja = $banyakBulan * 22;

        $persenKehadiran = $totalHariKerja > 0
            ? (($totalHariKerja - $totalTidakHadir) / $totalHariKerja) * 100
            : 0;

        $potonganS  = 0;
        $potonganI  = $totalI * 0.025 * $honorPerBulan;
        $potonganTK = $totalTK * 0.05 * $honorPerBulan;

        $totalPotongan = $potonganS + $potonganI + $potonganTK;

        $setelahPotongan = $jumlahKotor - $totalPotongan;

        $pph = 0;

        $jumlahBersih = $setelahPotongan - $pph;

        return [

            'nama' => $pegawai->nama_rekening,

            // DETAIL PER BULAN
            'detail_bulan' => $detailBulan,

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