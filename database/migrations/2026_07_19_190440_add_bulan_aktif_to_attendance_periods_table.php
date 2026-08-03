<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_periods', function (Blueprint $table) {
            // Bulan (1-12) yang sedang "dibuka" untuk diinput operator,
            // dalam triwulan periode ini. Diisi/diubah oleh superadmin.
            $table->unsignedTinyInteger('bulan_aktif')->nullable()->after('triwulan');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_periods', function (Blueprint $table) {
            $table->dropColumn('bulan_aktif');
        });
    }
};