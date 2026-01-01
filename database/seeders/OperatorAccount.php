<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class OperatorAccount extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/akun_madrasah.json');

        if (!File::exists($path)) {
            throw new \Exception("File akun_madrasah.json tidak ditemukan: $path");
        }

        $data = json_decode(File::get($path), true);

        if (!is_array($data)) {
            throw new \Exception("Format JSON tidak valid.");
        }

        DB::transaction(function () use ($data) {
            foreach ($data as $acc) {
                // pastikan field yang diperlukan ada
                if (!isset($acc['email'])) {
                    // skip atau bisa throw exception tergantung preferensi
                    continue;
                }

                // create jika belum ada, password hanya di-set saat create
                $user = User::firstOrCreate(
                    ['email' => $acc['email']],
                    [
                        'name'       => $acc['name'] ?? $acc['email'],
                        'username'   => $acc['username'] ?? null,
                        'unit_kerja' => $acc['unit_kerja'] ?? null,
                        'password'   => Hash::make('penmad123'),
                    ]
                );

                // selalu update field non-sensitive (tanpa merubah password)
                $user->update([
                    'name'       => $acc['name'] ?? $user->name,
                    'username'   => $acc['username'] ?? $user->username,
                    'unit_kerja' => $acc['unit_kerja'] ?? $user->unit_kerja,
                ]);

                // assign role (safe to call berulang kali jika paket role-mgmt menangani duplikat)
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('operator');
                }
            }
        });
    }
}