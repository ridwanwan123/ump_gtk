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
            'Nama Simpatika',
            'Nama Rekening',
            'Jabatan UMP',
            'Jabatan Dinas',
            'Status ASN',
            'No Rekening Bank DKI',
            'Madrasah',
            'NPSN Tempat Tugas',
            'NIK',
            'PEG ID',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Nama Ibu Kandung',
            'Agama',
            'Pendidikan Terakhir',
            'NPWP',
            'Nomor HP',
            'Email',
            'Alamat GTK',
            'Status Pegawai',
            'Dapodik',
        ];
    }

    public function map($pegawai): array
    {
        return [
            $pegawai->nama_simpatika,
            $pegawai->nama_rekening,
            $pegawai->jabatan_ump,
            $pegawai->jabatan_dinas,
            $pegawai->status_asn,
            "'" . $pegawai->no_rek_bank_dki,
            $pegawai->madrasah->nama_madrasah ?? '-',
            $pegawai->npsn_tempat_tugas,
            "'" . $pegawai->nik,
            "'" . $pegawai->pegid,
            $pegawai->tempat_lahir,
            $pegawai->tanggal_lahir,
            $pegawai->nama_ibu_kandung ?? '',
            $pegawai->agama,
            $pegawai->pend_terakhir,
            "'" . $pegawai->npwp,
            "'" . $pegawai->nomor_hp,
            $pegawai->alamat_email,
            $pegawai->alamat_gtk,
            $pegawai->status_pegawai,
            $pegawai->dapodik,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'L' => NumberFormat::FORMAT_DATE_DDMMYYYY, // tanggal lahir
        ];
    }
}
