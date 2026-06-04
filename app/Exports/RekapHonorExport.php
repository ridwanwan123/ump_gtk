<?php

namespace App\Exports;

use App\Models\Pegawai;
use App\Services\RekapHonorService;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class RekapHonorExport implements FromArray, ShouldAutoSize, WithStyles, WithEvents
{
    protected $bulan;
    protected $tahun;
    protected $honor;

    public function __construct($bulan, $tahun, $honor)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->honor = $honor;
    }

    public function array(): array
    {
        $service = new RekapHonorService();

        $pegawaiList = Pegawai::query()
            ->with('madrasah')
            ->leftJoin('madrasah', 'pegawai.id_madrasah', '=', 'madrasah.id')
            ->select('pegawai.*')
            ->orderBy('madrasah.nama_madrasah', 'asc')
            ->orderBy('pegawai.nama_rekening', 'asc')
            ->get();

        $rows = [];

        // HEADER (tetap sama)
        $header1 = [
            'NAMA SIMPATIKA','NAMA REKENING','JABATAN UMP','JABATAN DINAS',
            'STATUS ASN','NO REKENING BANK DKI','TEMPAT TUGAS','NPSN TEMPAT TUGAS',
            'NIK','PEG ID','TEMPAT LAHIR','TANGGAL LAHIR','NAMA IBU KANDUNG',
            'AGAMA','PENDIDIKAN TERAKHIR','NPWP','NO HP','EMAIL','ALAMAT GTK',
            'STATUS PEGAWAI','DAPODIK',
        ];

        foreach ($this->bulan as $b) {
            $namaBulan = strtoupper(
                \Carbon\Carbon::create()->month($b)->translatedFormat('F')
            );

            $header1[] = $namaBulan;
            $header1[] = '';
            $header1[] = '';
            $header1[] = '';
            $header1[] = '';
        }

        $header1 = array_merge($header1, [
            'JUMLAH KETIDAK HADIRAN',
            '', '', '', '', '',
            'BANYAK BULAN',
            '% KEHADIRAN',
            'HONOR / BULAN',
            'JUMLAH KOTOR',
            '0%',
            '2.5%',
            '5%',
            'TOTAL POTONGAN',
            'SETELAH POTONGAN',
            'PPH',
            'BERSIH',
        ]);

        $header2 = array_fill(0, 21, '');

        foreach ($this->bulan as $b) {
            $header2[] = 'S';
            $header2[] = 'I';
            $header2[] = 'TK';
            $header2[] = 'DL';
            $header2[] = 'C';
        }

        $header2 = array_merge($header2, [
            'S','I','TK','DL','C','JML',
            '','','','','','','','','','',''
        ]);

        $rows[] = $header1;
        $rows[] = $header2;

        // =========================
        // DATA
        // =========================

        foreach ($pegawaiList as $pegawai) {

            $row = $service->hitung(
                $pegawai,
                $this->bulan,
                $this->tahun,
                $this->honor
            );

            $data = [];

            // IDENTITAS
            $data[] = $pegawai->nama_simpatika ?? '';
            $data[] = $pegawai->nama_rekening ?? '';
            $data[] = $pegawai->jabatan_ump ?? '';
            $data[] = $pegawai->jabatan_dinas ?? '';
            $data[] = $pegawai->status_asn ?? '';
            $data[] = "'" . ($pegawai->no_rek_bank_dki ?? '');
            $data[] = $pegawai->madrasah->nama_madrasah ?? '';
            $data[] = $pegawai->npsn_tempat_tugas ?? '';
            $data[] = "'" . ($pegawai->nik ?? '');
            $data[] = "'" . ($pegawai->pegid ?? '');
            $data[] = $pegawai->tempat_lahir ?? '';
            $data[] = $pegawai->tanggal_lahir ?? '';
            $data[] = $pegawai->nama_ibu_kandung ?? '';
            $data[] = $pegawai->agama ?? '';
            $data[] = $pegawai->pend_terakhir ?? '';
            $data[] = "'" . ($pegawai->npwp ?? '');
            $data[] = "'" . ($pegawai->nomor_hp ?? '');
            $data[] = $pegawai->alamat_email ?? '';
            $data[] = $pegawai->alamat_gtk ?? '';
            $data[] = $pegawai->status_pegawai ?? '';
            $data[] = $pegawai->dapodik ?? '';

            // =========================
            // DETAIL BULAN (AMAN)
            // =========================

            foreach ($this->bulan as $bulan) {

                $d = $row['detail_bulan'][$bulan] ?? [
                    's' => 0,
                    'i' => 0,
                    'tk' => 0,
                    'dl' => 0,
                    'c' => 0,
                ];

                $data[] = $d['s'];
                $data[] = $d['i'];
                $data[] = $d['tk'];
                $data[] = $d['dl'];
                $data[] = $d['c'];
            }

            // =========================
            // TOTAL (SUDAH FIX LOGIC BARU)
            // =========================

            $data[] = $row['total_s'];
            $data[] = $row['total_i'];
            $data[] = $row['total_tk'];
            $data[] = $row['total_dl'];
            $data[] = $row['total_c'];

            $data[] = $row['total_s']
                    + $row['total_i']
                    + $row['total_tk']
                    + $row['total_dl']
                    + $row['total_c'];

            // ✔️ INI YANG SUDAH BENAR DARI HAK BAYAR
            $data[] = $row['banyak_bulan'];

            $data[] = $row['persen_kehadiran'] . '%';
            $data[] = $row['jumlah_honor_per_bulan'];
            $data[] = $row['jumlah_kotor'];

            $data[] = $row['potongan_s'];
            $data[] = $row['potongan_i'];
            $data[] = $row['potongan_tk'];

            $data[] = $row['total_potongan'];
            $data[] = $row['setelah_potongan'];
            $data[] = $row['pph'];
            $data[] = $row['jumlah_bersih'];

            $rows[] = $data;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                /*
                =========================
                STYLE HEADER
                =========================
                */

                $sheet->getStyle("A1:{$highestColumn}2")
                    ->applyFromArray([

                        'font' => [
                            'bold' => true,
                        ],

                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],

                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'EFF6FF']
                        ],

                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],

                    ]);

                /*
                =========================
                BORDER FULL
                =========================
                */

                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->applyFromArray([

                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CBD5E1']
                            ]
                        ]

                    ]);

                /*
                =========================
                MERGE BULAN
                =========================
                */

                $col = 22;

                foreach ($this->bulan as $b) {

                    $start = $this->columnLetter($col);
                    $end = $this->columnLetter($col + 4);

                    $sheet->mergeCells("{$start}1:{$end}1");

                    $col += 5;
                }

                /*
                =========================
                MERGE KETIDAKHADIRAN
                =========================
                */

                $startTotal = $this->columnLetter($col);
                $endTotal = $this->columnLetter($col + 5);

                $sheet->mergeCells("{$startTotal}1:{$endTotal}1");

                /*
                =========================
                MERGE IDENTITAS (2 ROW)
                =========================
                */

                for ($i = 1; $i <= 21; $i++) {

                    $colLetter = $this->columnLetter($i);

                    $sheet->mergeCells("{$colLetter}1:{$colLetter}2");
                }

                /*
                =========================
                FREEZE PANE
                =========================
                */

                $sheet->freezePane('A3');

                /*
                =========================
                PAGE SETUP
                =========================
                */

                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                $sheet->getPageSetup()
                        ->setFitToWidth(0)
                        ->setFitToHeight(0);

                /*
                =========================
                ROW HEIGHT
                =========================
                */

                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(24);
            }

        ];
    }

    private function columnLetter($c)
    {
        $letter = '';

        while ($c > 0) {
            $temp = ($c - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $c = intval(($c - $temp - 1) / 26);
        }

        return $letter;
    }
}