<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export Pusat Data: seluruh field pegawai, sesuai query (filter) yang dikirim controller.
 */
class PusatDataPegawaiExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithCustomValueBinder, WithStyles, WithTitle
{
    protected Builder $query;

    protected int $no = 0;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function title(): string
    {
        return 'Data Pegawai';
    }

    /**
     * Definisi kolom: [judul, pengambil nilai, tipe]. Tipe 'date' diformat dd/mm/yyyy.
     * Heading, mapping, dan format kolom semuanya diturunkan dari sini.
     */
    protected function columns(): array
    {
        return [
            ['No', fn ($p) => ++$this->no],
            ['Madrasah', fn ($p) => $p->madrasah?->nama_madrasah],
            ['Jenjang', fn ($p) => $p->madrasah?->type],
            ['NPSN Tempat Tugas', fn ($p) => $p->npsn_tempat_tugas],
            ['Nama Simpatika', fn ($p) => $p->nama_simpatika],
            ['Nama Rekening', fn ($p) => $p->nama_rekening],
            ['Jabatan UMP', fn ($p) => $p->jabatan_ump],
            ['Jabatan Dinas', fn ($p) => $p->jabatan_dinas],
            ['Status ASN', fn ($p) => $p->status_asn],
            ['No Rekening Bank DKI', fn ($p) => $p->no_rek_bank_dki],
            ['NIK', fn ($p) => $p->nik],
            ['PEG ID', fn ($p) => $p->pegid],
            ['Tempat Lahir', fn ($p) => $p->tempat_lahir],
            ['Tanggal Lahir', fn ($p) => $p->tanggal_lahir, 'date'],
            ['Nama Ibu Kandung', fn ($p) => $p->nama_ibu_kandung],
            ['Agama', fn ($p) => $p->agama],
            ['Pendidikan Terakhir', fn ($p) => $p->pend_terakhir],
            ['NPWP', fn ($p) => $p->npwp],
            ['Nomor HP', fn ($p) => $p->nomor_hp],
            ['Email', fn ($p) => $p->alamat_email],
            ['Alamat Domisili', fn ($p) => $p->alamat_gtk],
            ['Alamat Sesuai KTP', fn ($p) => $p->alamat_sesuai_ktp],
            ['Link KTP', fn ($p) => $p->link_drive_foto_ktp],
            ['Dapodik', fn ($p) => $p->dapodik],
            ['NIK Sesuai KTP', fn ($p) => $p->nik_sesuai],
            ['NIK Terdaftar EMIS 4.0', fn ($p) => $p->nik_terdaftar_emis40],
            ['Bukti EMIS 4.0 (Link)', fn ($p) => $p->link_drive_emis40],
            ['NIK Terdaftar EMIS GTK', fn ($p) => $p->nik_terdaftar_emis_gtk],
            ['Bukti EMIS GTK (Link)', fn ($p) => $p->link_drive_emis_gtk],
            ['Status Pegawai', fn ($p) => $p->status_pegawai],
            ['Alasan Nonaktif', fn ($p) => $p->alasan_mengundurkan_diri],
            ['Tanggal Nonaktif', fn ($p) => $p->tgl_nonaktif, 'date'],
            ['Alasan Ditolak', fn ($p) => $p->alasan_ditolak],
            ['Dibuat', fn ($p) => $p->created_at?->format('d/m/Y H:i')],
            ['Diperbarui', fn ($p) => $p->updated_at?->format('d/m/Y H:i')],
        ];
    }

    public function headings(): array
    {
        return array_column($this->columns(), 0);
    }

    public function map($pegawai): array
    {
        return array_map(function ($column) use ($pegawai) {
            $value = $column[1]($pegawai);

            if (($column[2] ?? null) === 'date') {
                return $this->toExcelDate($value);
            }

            return $value ?? '';
        }, $this->columns());
    }

    public function columnFormats(): array
    {
        $formats = [];

        foreach ($this->columns() as $i => $column) {
            if (($column[2] ?? null) === 'date') {
                $formats[Coordinate::stringFromColumnIndex($i + 1)] = NumberFormat::FORMAT_DATE_DDMMYYYY;
            }
        }

        return $formats;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->freezePane('A2');

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Semua teks ditulis sebagai teks murni, supaya NIK / no rekening / NPWP / no HP
     * tidak berubah jadi angka (1.23E+15, nol di depan hilang) dan isi yang diawali
     * "=" tidak dijalankan sebagai rumus.
     */
    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    protected function toExcelDate($value)
    {
        if (empty($value)) {
            return '';
        }

        try {
            return ExcelDate::PHPToExcel(Carbon::parse($value)->startOfDay());
        } catch (\Throwable $e) {
            // tanggal tidak valid di database -> tampilkan apa adanya
            return (string) $value;
        }
    }
}
