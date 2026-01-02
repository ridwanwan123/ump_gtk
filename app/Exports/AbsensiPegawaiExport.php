<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AbsensiPegawaiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $tahun;
    protected $tw;

    public function __construct($tahun, $tw)
    {
        $this->tahun = $tahun;
        $this->tw = $tw;
    }

    public function collection()
    {
        $bulanPerTW = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        $bulanTW = $bulanPerTW[$this->tw];

        return Pegawai::whereHas('absensi', function ($q) use ($bulanTW) {
                $q->where('tahun', $this->tahun)
                  ->whereIn('bulan', $bulanTW);
            })
            ->with([
                'madrasah',
                'absensi' => function ($q) use ($bulanTW) {
                    $q->where('tahun', $this->tahun)
                      ->whereIn('bulan', $bulanTW);
                }
            ])
            ->orderBy('id_madrasah')
            ->orderBy('nama_rekening')
            ->get();
    }

    public function headings(): array
    {
        $subHeaders = ['S', 'I', 'TK', 'DL', 'C'];

        $bulanPerTW = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        $row1 = [
            'NAMA SESUAI REKENING BANK DKI DAN URUT ABJAD',
            'MADRASAH'
        ];

        $row2 = ['', ''];

        foreach ($bulanPerTW[$this->tw] as $bulan) {
            foreach ($subHeaders as $sh) {
                $row1[] = '';
                $row2[] = $sh;
            }
        }

        return [$row1, $row2];
    }

    public function map($pegawai): array
    {
        $row = [
            $pegawai->nama_rekening,
            $pegawai->madrasah->nama_madrasah ?? '-',
        ];

        $bulanPerTW = [
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
        ];

        $absensiByBulan = $pegawai->absensi->keyBy('bulan');

        foreach ($bulanPerTW[$this->tw] as $bulan) {
            $a = $absensiByBulan[$bulan] ?? null;
            $row[] = $a->sakit ?? 0;
            $row[] = $a->izin ?? 0;
            $row[] = $a->ketidakhadiran ?? 0;
            $row[] = $a->dinas_luar ?? 0;
            $row[] = $a->cuti ?? 0;
        }

        return $row;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');

                $bulanPerTW = [
                    1 => [1, 2, 3],
                    2 => [4, 5, 6],
                    3 => [7, 8, 9],
                    4 => [10, 11, 12],
                ];

                $startCol = 3; // kolom C
                $subHeadersCount = 5;

                $numToLetter = function ($num) {
                    $letters = '';
                    while ($num > 0) {
                        $mod = ($num - 1) % 26;
                        $letters = chr(65 + $mod) . $letters;
                        $num = (int)(($num - $mod) / 26);
                    }
                    return $letters;
                };

                foreach ($bulanPerTW[$this->tw] as $i => $bulan) {
                    $start = $startCol + ($i * $subHeadersCount);
                    $end = $start + $subHeadersCount - 1;

                    $colStart = $numToLetter($start);
                    $colEnd = $numToLetter($end);

                    $sheet->mergeCells("{$colStart}1:{$colEnd}1");

                    $bulanNama = [
                        1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET',
                        4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI',
                        7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER',
                        10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER',
                    ];

                    $sheet->setCellValue("{$colStart}1", $bulanNama[$bulan]);

                    $sheet->getStyle("{$colStart}1:{$colEnd}2")->getFont()->setBold(true);
                    $sheet->getStyle("{$colStart}1:{$colEnd}2")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->getStyle('A1:B2')->getFont()->setBold(true);
                $sheet->getStyle('A1:B2')->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $lastCol = $numToLetter($startCol + count($bulanPerTW[$this->tw]) * $subHeadersCount - 1);
                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:{$lastCol}2")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("A3:{$lastCol}{$highestRow}")->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("C3:{$lastCol}{$highestRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
