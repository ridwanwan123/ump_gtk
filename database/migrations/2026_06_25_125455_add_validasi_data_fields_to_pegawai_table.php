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
        Schema::table('pegawai', function (Blueprint $table) {
            $table->enum('nik_sesuai', ['YA', 'TIDAK'])->nullable();
            $table->enum('nik_terdaftar_emis40', ['YA', 'TIDAK'])->nullable();
            $table->text('link_drive_emis40')->nullable();
            $table->enum('nik_terdaftar_emis_gtk', ['YA', 'TIDAK'])->nullable();
            $table->text('link_drive_emis_gtk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn([
                'nik_sesuai',
                'nik_terdaftar_emis40',
                'link_drive_emis40',
                'nik_terdaftar_emis_gtk',
                'link_drive_emis_gtk',
            ]);
        });
    }
};
