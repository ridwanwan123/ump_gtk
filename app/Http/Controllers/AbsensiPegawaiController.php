<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsensiPegawai;
use App\Models\Pegawai; // import model Pegawai
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AbsensiPegawaiController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('viewAny', AbsensiPegawai::class);

        // Default TW berdasarkan bulan sekarang
        $currentMonth = date('n'); // 1-12
        $tw = $request->tw ?? ceil($currentMonth / 3); // Januari-Maret=1, dst.

        // Definisikan bulan per TW
        $bulanTriwulan = [
            1 => ['Januari', 'Februari', 'Maret'],
            2 => ['April', 'Mei', 'Juni'],
            3 => ['Juli', 'Agustus', 'September'],
            4 => ['Oktober', 'November', 'Desember'],
        ];

        // Ambil data pegawai + absensi sesuai TW
        $pegawaiList = Pegawai::with([
                'madrasah',
                'absensi' => fn($q) => $q->where('tw', $tw)
            ])
            ->orderBy('nama_rekening')
            ->get()
            ->map(fn($p) => (object)[
                'nama_madrasah' => $p->madrasah->nama_madrasah ?? '-',
                'nama_rekening' => $p->nama_rekening,
                'jabatan'       => $p->jabatan,
                's'             => $p->absensi->first()?->sakit ?? 0,
                'i'             => $p->absensi->first()?->izin ?? 0,
                'kt'            => $p->absensi->first()?->ketidakhadiran ?? 0,
                'dl'            => $p->absensi->first()?->dinas_luar ?? 0,
                'c'             => $p->absensi->first()?->cuti ?? 0,
            ]);

        // Hitung total absensi per kategori
        $totalAbsensi = [
            's'  => $pegawaiList->sum('s'),
            'i'  => $pegawaiList->sum('i'),
            'kt' => $pegawaiList->sum('kt'),
            'dl' => $pegawaiList->sum('dl'),
            'c'  => $pegawaiList->sum('c'),
        ];

        return view('absensi.index', [
            'pegawaiList'    => $pegawaiList,
            'tw'             => $tw,
            'bulanTriwulan'  => $bulanTriwulan[$tw],
            'totalAbsensi'   => $totalAbsensi,
        ]);
    }


    public function create(Request $request)
    {
        $this->authorize('viewAny', AbsensiPegawai::class);

        $tw = $request->tw ?? ceil(now()->month / 3);

        $bulanPerTW = [
            1 => ['Januari','Februari','Maret'],
            2 => ['April','Mei','Juni'],
            3 => ['Juli','Agustus','September'],
            4 => ['Oktober','November','Desember'],
        ];

        $bulan = $bulanPerTW[$tw];

        // 🔥 tampilkan semua pegawai
        $pegawaiList = Pegawai::orderBy('nama_rekening')->get();

        return view('absensi.create', compact('pegawaiList', 'bulan', 'tw'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AbsensiPegawai::class);

        $tw = $request->tw;
        $absensiData = $request->input('absensi', []);

        $pegawaiList = Pegawai::all();

        DB::transaction(function () use ($pegawaiList, $absensiData, $tw) {

            foreach ($pegawaiList as $pegawai) {

                // default 0
                $total = [
                    'sakit' => 0,
                    'izin' => 0,
                    'ketidakhadiran' => 0,
                    'dinas_luar' => 0,
                    'cuti' => 0,
                ];

                // jumlahkan 3 bulan
                if (isset($absensiData[$pegawai->id])) {
                    foreach ($absensiData[$pegawai->id] as $bulanData) {
                        foreach ($total as $key => $val) {
                            $total[$key] += (int) ($bulanData[$key] ?? 0);
                        }
                    }
                }

                AbsensiPegawai::updateOrCreate(
                    [
                        'pegawai_id' => $pegawai->id,
                        'tw' => $tw,
                    ],
                    $total
                );
            }
        });

        return back()->with('success', 'Absensi triwulan berhasil disimpan.');
    }
}
