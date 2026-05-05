<?php

namespace App\Exports;

use App\Models\Pegawai;
use App\Services\RekapHonorService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RekapHonorExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $bulan;
    protected $tahun;
    protected $honor;
    protected $service;

    public function __construct($bulan, $tahun, $honor)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->honor = $honor;
        $this->service = new RekapHonorService();
    }

    public function collection()
    {
        return Pegawai::with('madrasah')
            ->join('madrasah', 'pegawai.id_madrasah', '=', 'madrasah.id')
            ->orderBy('madrasah.nama_madrasah', 'asc')
            ->orderBy('pegawai.nama_rekening', 'asc')
            ->select('pegawai.*')
            ->get();
    }

    public function map($pegawai): array
    {
        $data = $this->service->hitung(
            $pegawai,
            $this->bulan,
            $this->tahun,
            $this->honor
        );

        return [
            $pegawai->madrasah->nama_madrasah ?? '-',
            $data['nama'],

            // ABSENSI
            $data['total_s'],
            $data['total_i'],
            $data['total_tk'],
            $data['total_dl'],
            $data['total_c'],
            $data['total_s'] + $data['total_i'] + $data['total_tk'] + $data['total_dl'] + $data['total_c'],

            // BASIC
            $data['banyak_bulan'],
            $data['persen_kehadiran'],
            $data['jumlah_honor_per_bulan'],
            $data['jumlah_kotor'],

            // POTONGAN
            0,
            $data['potongan_i'],
            $data['potongan_tk'],

            // FINAL
            $data['total_potongan'],
            $data['setelah_potongan'],
            $data['pph'],
            $data['jumlah_bersih'],
        ];
    }

    public function headings(): array
    {
        return [
            [
                'MADRASAH', 'NAMA',
                'S', 'I', 'TK', 'DL', 'C', 'JML',
                'BANYAK BULAN', '% KEHADIRAN', 'HONOR', 'KOTOR',
                '0%', '2.5%', '5%',
                'TOTAL POTONGAN', 'SETELAH POTONGAN', 'PPH', 'BERSIH'
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function ($event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('A1:S1')
                    ->getFont()->setBold(true);

                $sheet->getStyle('A1:S1')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A1:S' . $sheet->getHighestRow())
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}