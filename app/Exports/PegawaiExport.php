<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PegawaiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    /**
     * Ambil semua data Pegawai
     */
    public function collection()
    {
        return Pegawai::with('madrasah')->get();
    }

    /**
     * Heading Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Pegawai',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Nama Ibu Kandung',
            'Pendidikan Terakhir',
            'Alamat',
            'Jabatan',
            'PEG ID',
            'Madrasah',
            'NPWP',
            'Nomor HP',
            'Email',
            'No Rekening Bank DKI',
        ];
    }

    /**
     * Map data agar format sesuai
     */
    public function map($pegawai): array
    {
        return [
            $pegawai->id,
            $pegawai->nama_rekening,
            "'" . $pegawai->nik,                  // paksa string
            $pegawai->tempat_lahir,
            $pegawai->tanggal_lahir,
            $pegawai->nama_ibu_kandung,
            $pegawai->pend_terakhir,
            $pegawai->alamat_gtk,
            $pegawai->jabatan,
            "'" . $pegawai->pegid,                // paksa string
            $pegawai->madrasah->nama_madrasah ?? '-',
            "'" . $pegawai->npwp,                 // paksa string
            "'" . $pegawai->nomor_hp,             // paksa string
            $pegawai->alamat_email,
            "'" . $pegawai->no_rek_bank_dki,     // paksa string
        ];
    }

    /**
     * Format kolom, tetap bisa dipakai untuk tanggal atau angka lain
     */
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY, // kolom Tanggal Lahir
        ];
    }
}
