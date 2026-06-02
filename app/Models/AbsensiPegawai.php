<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Pegawai;

class AbsensiPegawai extends Model
{
    use HasFactory;

    protected $table = 'absensi_pegawai';
    
    protected $fillable = [
        'pegawai_id',
        'bulan',
        'tahun',
        'tw',
        'sakit',
        'izin',
        'ketidakhadiran',
        'dinas_luar',
        'cuti',
        'banyaknya_bulan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
