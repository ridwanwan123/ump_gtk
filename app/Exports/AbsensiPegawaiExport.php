<?php

namespace App\Exports;

use App\Models\Pegawai;
use App\Services\RekapHonorService;
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
        'NAMA SIMPATIKA',
        'NAMA REKENING',
        'JABATAN UMP',
        'JABATAN DINAS',
        'STATUS ASN',
        'NO REKENING BANK DKI',
        'TEMPAT TUGAS',
        'NPSN TEMPAT TUGAS',
        'NIK',
        'PEG ID',
        'TEMPAT LAHIR',
        'TANGGAL LAHIR',
        'NAMA IBU KANDUNG',
        'AGAMA',
        'PENDIDIKAN TERAKHIR',
        'NPWP',
        'NO HP',
        'EMAIL',
        'ALAMAT GTK',
        'STATUS PEGAWAI',
        'DAPODIK',
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

    public function headings(): array
    {
        $row1 = $this->pegawaiHeaders;
        $row2 = array_fill(0, count($this->pegawaiHeaders), '');

        // ABSENSI BULAN
        foreach ($this->bulanPerTW[$this->tw] as $bulan) {
            foreach ($this->subHeaders as $sh) {
                $row1[] = '';
                $row2[] = $sh;
            }
        }

        // TOTAL ABSENSI
        $jumlahHeaders = ['S', 'I', 'TK', 'DL', 'C', 'JML'];

        foreach ($jumlahHeaders as $jh) {
            $row1[] = '';
            $row2[] = $jh;
        }

        // REKAP HONOR HEADER
        $rekapHeaders = [
            'BANYAK BULAN',
            '% KEHADIRAN',
            'HONOR / BULAN',
            'JUMLAH KOTOR',
            'POTONGAN S',
            'POTONGAN I',
            'POTONGAN TK',
            'TOTAL POTONGAN',
            'SETELAH POTONGAN',
            'PPH',
            'JUMLAH BERSIH'
        ];

        foreach ($rekapHeaders as $h) {
            $row1[] = '';
            $row2[] = $h;
        }

        return [$row1, $row2];
    }

    public function map($pegawai): array
    {
        $row = [
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
            $pegawai->nama_ibu_kandung,
            $pegawai->agama,
            $pegawai->pend_terakhir,
            "'" . $pegawai->npwp,
            "'" . $pegawai->nomor_hp,
            $pegawai->alamat_email,
            $pegawai->alamat_gtk,
            $pegawai->status_pegawai,
            $pegawai->dapodik,
        ];

        $absensiByBulan = $pegawai->absensi->keyBy('bulan');

        $totalS = 0;
        $totalI = 0;
        $totalTK = 0;
        $totalDL = 0;
        $totalC = 0;

        foreach ($this->bulanPerTW[$this->tw] as $bulan) {
            $a = $absensiByBulan[$bulan] ?? null;

            $s  = $a->sakit ?? 0;
            $i  = $a->izin ?? 0;
            $tk = $a->ketidakhadiran ?? 0;
            $dl = $a->dinas_luar ?? 0;
            $c  = $a->cuti ?? 0;

            $totalS += $s;
            $totalI += $i;
            $totalTK += $tk;
            $totalDL += $dl;
            $totalC += $c;

            $row[] = $s;
            $row[] = $i;
            $row[] = $tk;
            $row[] = $dl;
            $row[] = $c;
        }

        $totalAll = $totalS + $totalI + $totalTK + $totalDL + $totalC;

        $row[] = $totalS;
        $row[] = $totalI;
        $row[] = $totalTK;
        $row[] = $totalDL;
        $row[] = $totalC;
        $row[] = $totalAll;

        // =========================
        // REKAP HONOR SERVICE
        // =========================
        $service = new RekapHonorService();

        $rekap = $service->hitung(
            $pegawai,
            $this->bulanPerTW[$this->tw],
            $this->tahun,
            3900000 // bisa nanti dari input
        );

        $row[] = $rekap['banyak_bulan'];
        $row[] = $rekap['persen_kehadiran'];
        $row[] = $rekap['jumlah_honor_per_bulan'];
        $row[] = $rekap['jumlah_kotor'];

        $row[] = $rekap['potongan_s'];
        $row[] = $rekap['potongan_i'];
        $row[] = $rekap['potongan_tk'];

        $row[] = $rekap['total_potongan'];
        $row[] = $rekap['setelah_potongan'];
        $row[] = $rekap['pph'];
        $row[] = $rekap['jumlah_bersih'];

        return $row;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $numToLetter = function ($num) {
                    $letters = '';
                    while ($num > 0) {
                        $mod = ($num - 1) % 26;
                        $letters = chr(65 + $mod) . $letters;
                        $num = (int)(($num - $mod) / 26);
                    }
                    return $letters;
                };

                // ======================
                // PEGAWAI HEADER MERGE
                // ======================
                for ($i = 1; $i <= count($this->pegawaiHeaders); $i++) {
                    $col = $numToLetter($i);
                    $sheet->mergeCells("{$col}1:{$col}2");
                }

                $startCol = count($this->pegawaiHeaders) + 1;
                $subCount = count($this->subHeaders);

                // ======================
                // BULAN HEADER
                // ======================
                foreach ($this->bulanPerTW[$this->tw] as $i => $bulan) {
                    $start = $startCol + ($i * $subCount);
                    $end = $start + $subCount - 1;

                    $colStart = $numToLetter($start);
                    $colEnd = $numToLetter($end);

                    $sheet->mergeCells("{$colStart}1:{$colEnd}1");
                }

                // ======================
                // TOTAL ABSENSI
                // ======================
                $jumlahColCount = 6;

                $startJumlah = $startCol + (count($this->bulanPerTW[$this->tw]) * $subCount);
                $endJumlah = $startJumlah + $jumlahColCount - 1;

                // ======================
                // REKAP OFFSET
                // ======================
                $startRekap = $endJumlah + 1;
                $endRekap = $startRekap + 10;

                $colStartRekap = $numToLetter($startRekap);
                $colEndRekap = $numToLetter($endRekap);

                $sheet->mergeCells("{$colStartRekap}1:{$colEndRekap}1");
                $sheet->setCellValue("{$colStartRekap}1", 'REKAP HONOR');

                // STYLE
                $sheet->getStyle("A1:{$colEndRekap}2")
                    ->getFont()->setBold(true);

                $sheet->getStyle("A1:{$colEndRekap}2")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("A1:{$colEndRekap}" . $sheet->getHighestRow())
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
        ];
    }
}