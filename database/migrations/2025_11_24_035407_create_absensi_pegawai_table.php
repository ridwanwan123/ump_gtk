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
        Schema::create('absensi_pegawai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pegawai_id');
            $table->integer('sakit')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('ketidakhadiran')->default(0);
            $table->integer('dinas_luar')->default(0);
            $table->integer('cuti')->default(0);
            $table->integer('tw');
            $table->integer('tahun');
            $table->timestamps();

            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_pegawai');
    }
};
