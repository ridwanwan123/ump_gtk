<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MadrasahSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/madrasah.json');

        if (!File::exists($path)) {
            throw new \Exception("File madrasah.json tidak ditemukan: $path");
        }

        $data = json_decode(File::get($path), true);

        if (!is_array($data)) {
            throw new \Exception("Format JSON tidak valid.");
        }

        // Chunk untuk performa
        $chunks = array_chunk($data, 300);

        DB::transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                DB::table('madrasah')->upsert(
                    $chunk,
                    ['nama_madrasah'],            // unique key
                    ['alamat_madrasah', 'type']   // fields to update if exists
                );
            }
        });
    }
}
