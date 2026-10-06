<?php

namespace App\Exports;

use App\Models\Pegawai;
use Carbon\Carbon;

/**
 * Export pegawai yang akan pensiun: kolom pensiun + seluruh field pegawai.
 */
class PusatDataPensiunExport extends PusatDataPegawaiExport
{
    public function title(): string
    {
        return 'Data Pensiun';
    }

    protected function columns(): array
    {
        $columns = parent::columns();

        $pensiun = [
            ['Usia (Tahun)', fn ($p) => Carbon::parse($p->tanggal_lahir)->age],
            ['Usia Pensiun', fn ($p) => Pegawai::usiaPensiun($p->jabatan_dinas)],
            ['Tanggal Pensiun', fn ($p) => $this->tanggalPensiun($p), 'date'],
            ['Sisa Waktu', fn ($p) => $this->sisaWaktu($p)],
        ];

        // sisipkan setelah kolom "No"
        array_splice($columns, 1, 0, $pensiun);

        return $columns;
    }

    protected function tanggalPensiun($pegawai): Carbon
    {
        return Carbon::parse($pegawai->tanggal_lahir)
            ->addYears(Pegawai::usiaPensiun($pegawai->jabatan_dinas));
    }

    protected function sisaWaktu($pegawai): string
    {
        $tanggalPensiun = $this->tanggalPensiun($pegawai)->startOfDay();
        $hariIni = now()->startOfDay();

        if ($tanggalPensiun->lte($hariIni)) {
            return 'Sudah melewati usia pensiun';
        }

        $selisih = $hariIni->diff($tanggalPensiun);

        return "{$selisih->y} tahun {$selisih->m} bulan";
    }
}
