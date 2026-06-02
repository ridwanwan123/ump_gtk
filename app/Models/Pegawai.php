<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Madrasah;
use App\Models\AbsensiPegawai;

class Pegawai extends Model
{
    const AKTIF                 = 'AKTIF';
    const PROSES_NON_AKTIF      = 'PROSES NONAKTIF'; //sisi operator
    const NON_AKTIF             = 'NON AKTIF'; //sisi admin pusat
    const PROSES_USULAN         = 'PROSES USULAN'; //sisi operator
    const USULAN                = 'USULAN'; //sisi admin pusat

    const ALASAN = [
                        'MENINGGAL DUNIA',
                        'PENSIUN',
                        'DIANGKAT P3K',
                        'DIANGKAT CPNS/PNS',
                        'PINDAH KERJA',
                        'MENGUNDURKAN DIRI',
                        'LAINNYA',
                    ];

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
        'alamat_sesuai_ktp',
        'link_drive_foto_ktp',
        'status_pegawai',
        'dapodik',
        'alasan_mengundurkan_diri',
        'tgl_nonaktif'
    ];

    protected static function booted()
    {
        // ✅ Scope Madrasah (existing)
        static::addGlobalScope('madrasah', function (Builder $builder) {
            $mid = app()->has('current_madrasah_id')
                ? app('current_madrasah_id')
                : null;

            if ($mid !== null) {
                $builder->where('id_madrasah', $mid);
            }
        });

        // ✅ Scope Status AKTIF (tambahan baru)
        static::addGlobalScope('aktif', function (Builder $builder) {
            $builder->where('status_pegawai', self::AKTIF);
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