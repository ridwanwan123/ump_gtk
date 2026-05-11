<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendancePeriod extends Model
{
    use HasFactory;

    protected $table = 'attendance_periods';

    protected $fillable = [
        'tahun',
        'triwulan',
        'is_active'
    ];
}
