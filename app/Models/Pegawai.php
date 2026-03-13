<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Madrasah;
use App\Models\AbsensiPegawai;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai'; // sesuai tabel Anda
    protected $fillable = [
        'nama_simpatika',
        'nama_rekening',
        'jabatan_ump',
        'jabatan_dinas',
        'status_asn',
        'no_rek_bank_dki',
        'id_madrasah',
        'npsn_tempat_tugas',
        'nik',
        'pegid',
        'tempat_lahir',
        'tanggal_lahir',
        'nama_ibu_kandung',
        'agama',
        'pend_terakhir',
        'npwp',
        'nomor_hp',
        'alamat_email',
        'alamat_gtk',
        'status_pegawai',
        'dapodik'
    ];

    protected static function booted()
    {
        static::addGlobalScope('madrasah', function (Builder $builder) {
            // ambil nilai yang diset middleware
           $mid = app()->has('current_madrasah_id')
                ? app('current_madrasah_id')
                : null;

            // jika ada current madrasah id (operator), filter
            if ($mid !== null) {
                $builder->where('id_madrasah', $mid);
            }
            // jika null -> superadmin -> tidak ditambahkan where, lihat semua
        });
    }

    public function madrasah()
    {
        return $this->belongsTo(Madrasah::class, 'id_madrasah');
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiPegawai::class, 'pegawai_id');
    }
}