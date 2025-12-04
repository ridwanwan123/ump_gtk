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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rekening');          // nama sesuai rekening
            $table->string('jabatan')->nullable();    // jabatan
            $table->text('alamat_gtk')->nullable();   // alamat GTK
            $table->string('no_rek_bank_dki')->nullable(); // nomor rekening
            $table->foreignId('id_madrasah')->constrained('madrasah');    // tempat tugas (unit_kerja)
            $table->string('nik')->nullable();             // NIK
            $table->string('pegid')->nullable();           // pegid
            $table->string('tempat_lahir')->nullable();    // tempat lahir
            $table->date('tanggal_lahir')->nullable();     // tanggal lahir
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('pend_terakhir')->nullable();
            $table->string('npwp')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('alamat_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
