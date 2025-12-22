<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdmin extends Seeder
{
    public function run()
    {
        $user = User::firstOrCreate(
            ['email' => 'superadmin@test.local'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('penmad123'),
                'unit_kerja' => NULL,
            ]
        );

        // Assign role superadmin
        if (!$user->hasRole('superadmin')) {
            $user->assignRole('superadmin');
        }
    }
}
