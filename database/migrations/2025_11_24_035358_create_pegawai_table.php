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

            $table->string('nama_simpatika')->nullable();
            $table->string('nama_rekening');
            $table->string('jabatan_ump')->nullable();
            $table->string('jabatan_dinas')->nullable();
            $table->string('status_asn')->nullable();
            $table->string('no_rek_bank_dki')->nullable();
            $table->foreignId('id_madrasah')->constrained('madrasah'); // FK ke madrasah
            $table->string('npsn_tempat_tugas')->nullable();

            $table->string('nik')->nullable();
            $table->string('pegid')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('agama')->nullable();
            $table->string('pend_terakhir')->nullable();
            $table->string('npwp')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('alamat_email')->nullable();
            $table->text('alamat_gtk')->nullable();
            $table->string('status_pegawai')->nullable();
            $table->string('dapodik')->nullable();

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
