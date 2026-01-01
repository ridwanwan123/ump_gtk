<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Madrasah extends Model
{
    use HasFactory;

    protected $table = 'madrasah';

    protected $fillable = [
        'nama_madrasah'
    ];

    // ===========================
    // Relasi ke pegawai
    // ===========================
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_madrasah'); 
        // id_madrasah adalah foreign key di tabel pegawai
    }
}
