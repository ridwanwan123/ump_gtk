<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakPembayaranPegawai extends Model
{
    use HasFactory;

    protected $table = 'hak_pembayaran_pegawai';

    protected $fillable = [
        'pegawai_id',
        'bulan',
        'tahun',
        'tw',
        'jumlah_hak'
    ];

    const BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
}
