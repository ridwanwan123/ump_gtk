<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensi_pegawai', function (Blueprint $table) {
            $table->integer('banyaknya_bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_pegawai', function (Blueprint $table) {
            $table->dropColumn('banyaknya_bulan');
        });
    }
};
