<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BendaharaAccount extends Seeder
{
    public function run()
    {
        // MAN 1 - 22
        for ($i = 1; $i <= 22; $i++) {
            $unit = "MAN $i";
            $email = "bendahara.man$i@test.local";

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Bendahara $unit",
                    'username' => "bendahara_man_$i",
                    'password' => Hash::make('penmad123'),
                    'unit_kerja' => $unit,
                ]
            );

            $user->assignRole('bendahara');
        }

        // MTSN 1 - 42
        for ($i = 1; $i <= 42; $i++) {
            $unit = "MTSN $i";
            $email = "bendahara.mtsn$i@test.local";

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Bendahara $unit",
                    'username' => "bendahara_mtsn_$i",
                    'password' => Hash::make('penmad123'),
                    'unit_kerja' => $unit,
                ]
            );

            $user->assignRole('bendahara');
        }

        // MIN 1 - 22
        for ($i = 1; $i <= 22; $i++) {
            $unit = "MIN $i";
            $email = "bendahara.min$i@test.local";

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Bendahara $unit",
                    'username' => "bendahara_min_$i",
                    'password' => Hash::make('penmad123'),
                    'unit_kerja' => $unit,
                ]
            );

            $user->assignRole('bendahara');
        }
    }
}