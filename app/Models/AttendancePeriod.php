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
        'is_active',
        'bulan_aktif',
    ];

    const BULAN_PER_TW = [
        'TW 1' => [1, 2, 3],
        'TW 2' => [4, 5, 6],
        'TW 3' => [7, 8, 9],
        'TW 4' => [10, 11, 12],
    ];

    /**
     * Daftar bulan (angka) milik triwulan periode ini. Misal TW 1 -> [1,2,3]
     */
    public function getBulanListAttribute(): array
    {
        return self::BULAN_PER_TW[$this->triwulan] ?? [];
    }

    /**
     * Bulan-bulan yang sudah "dibuka" untuk input/rekap, yaitu bulan pertama
     * triwulan sampai dengan bulan_aktif saat ini. Dipakai supaya rekap (index)
     * tampil progresif/dinamis: bulan 1 muncul dulu, lalu nambah begitu
     * superadmin membuka bulan 2, dst.
     */
    public function getBulanTerbukaAttribute(): array
    {
        $list = $this->bulan_list;
        $idx  = array_search($this->bulan_aktif, $list, true);

        return $idx === false ? [] : array_slice($list, 0, $idx + 1);
    }

    /**
     * Nomor triwulan (1-4) dalam bentuk integer, dari format string "TW 1".
     */
    public function getTwNumberAttribute(): int
    {
        return (int) str_replace('TW ', '', $this->triwulan);
    }
}