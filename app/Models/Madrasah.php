<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Madrasah extends Model
{
    use HasFactory;

    protected $table = 'madrasah';

    protected  $fillable = [
        'nama_madrasah'
    ];
}
