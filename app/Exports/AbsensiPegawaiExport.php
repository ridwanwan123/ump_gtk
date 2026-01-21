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

class AbsensiPegawaiExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents
{
    protected $tahun;
    protected $tw;

    protected $pegawaiHeaders = [
        'ID',
        'NAMA PEGAWAI',
        'NIK',
        'TEMPAT LAHIR',
        'TANGGAL LAHIR',
        'NAMA IBU KANDUNG',
        'PENDIDIKAN TERAKHIR',
        'ALAMAT',
        'JABATAN',
        'PEG ID',
        'MADRASAH',
        'NPWP',
        'NO HP',
        'EMAIL',
        'NO REKENING BANK DKI',
    ];

    protected $bulanPerTW = [
        1 => [1, 2, 3],
        2 => [4, 5, 6],
        3 => [7, 8, 9],
        4 => [10, 11, 12],
    ];

    protected $subHeaders = ['S', 'I', 'TK', 'DL', 'C'];

    public function __construct($tahun, $tw)
    {
        $this->tahun = $tahun;
        $this->tw = $tw;
    }

    /**
     * Ambil data pegawai + absensi
     */
    public function collection()
    {
        return Pegawai::whereHas('absensi', function ($q) {
                $q->where('tahun', $this->tahun)
                  ->whereIn('bulan', $this->bulanPerTW[$this->tw]);
            })
            ->with([
                'madrasah',
                'absensi' => function ($q) {
                    $q->where('tahun', $this->tahun)
                      ->whereIn('bulan', $this->bulanPerTW[$this->tw]);
                }
            ])
            ->orderBy('id_madrasah')
            ->orderBy('nama_rekening')
            ->get();
    }

    /**
     * Heading Excel (2 baris)
     */
    public function headings(): array
    {
        $row1 = $this->pegawaiHeaders;
        $row2 = array_fill(0, count($this->pegawaiHeaders), '');

        foreach ($this->bulanPerTW[$this->tw] as $bulan) {
            foreach ($this->subHeaders as $sh) {
                $row1[] = '';
                $row2[] = $sh;
            }
        }

        return [$row1, $row2];
    }

    /**
     * Mapping data per baris
     */
    public function map($pegawai): array
    {
        $row = [
            $pegawai->id,
            $pegawai->nama_rekening,
            "'" . $pegawai->nik,
            $pegawai->tempat_lahir,
            $pegawai->tanggal_lahir,
            $pegawai->nama_ibu_kandung,
            $pegawai->pend_terakhir,
            $pegawai->alamat_gtk,
            $pegawai->jabatan,
            "'" . $pegawai->pegid,
            $pegawai->madrasah->nama_madrasah ?? '-',
            "'" . $pegawai->npwp,
            "'" . $pegawai->nomor_hp,
            $pegawai->alamat_email,
            "'" . $pegawai->no_rek_bank_dki,
        ];

        $absensiByBulan = $pegawai->absensi->keyBy('bulan');

        foreach ($this->bulanPerTW[$this->tw] as $bulan) {
            $a = $absensiByBulan[$bulan] ?? null;
            $row[] = $a->sakit ?? 0;
            $row[] = $a->izin ?? 0;
            $row[] = $a->ketidakhadiran ?? 0;
            $row[] = $a->dinas_luar ?? 0;
            $row[] = $a->cuti ?? 0;
        }

        return $row;
    }

    /**
     * Styling & merge cell
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // helper number → column letter
                $numToLetter = function ($num) {
                    $letters = '';
                    while ($num > 0) {
                        $mod = ($num - 1) % 26;
                        $letters = chr(65 + $mod) . $letters;
                        $num = (int)(($num - $mod) / 26);
                    }
                    return $letters;
                };

                // Merge header pegawai (A1:A2 dst)
                for ($i = 1; $i <= count($this->pegawaiHeaders); $i++) {
                    $col = $numToLetter($i);
                    $sheet->mergeCells("{$col}1:{$col}2");
                }

                // Header bulan
                $startCol = count($this->pegawaiHeaders) + 1;
                $subCount = count($this->subHeaders);

                $bulanNama = [
                    1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET',
                    4 => 'APRIL', 5 => 'MEI', 6 => 'JUNI',
                    7 => 'JULI', 8 => 'AGUSTUS', 9 => 'SEPTEMBER',
                    10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER',
                ];

                foreach ($this->bulanPerTW[$this->tw] as $i => $bulan) {
                    $start = $startCol + ($i * $subCount);
                    $end   = $start + $subCount - 1;

                    $colStart = $numToLetter($start);
                    $colEnd   = $numToLetter($end);

                    $sheet->mergeCells("{$colStart}1:{$colEnd}1");
                    $sheet->setCellValue("{$colStart}1", $bulanNama[$bulan]);

                    $sheet->getStyle("{$colStart}1:{$colEnd}2")
                        ->getFont()->setBold(true);

                    $sheet->getStyle("{$colStart}1:{$colEnd}2")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }

                // Style header pegawai
                $lastCol = $numToLetter(
                    $startCol + (count($this->bulanPerTW[$this->tw]) * $subCount) - 1
                );

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:{$lastCol}2")
                    ->getFont()->setBold(true);

                $sheet->getStyle("A1:{$lastCol}2")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Border header & body
                $sheet->getStyle("A1:{$lastCol}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Center angka absensi
                $sheet->getStyle("{$numToLetter($startCol)}3:{$lastCol}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
