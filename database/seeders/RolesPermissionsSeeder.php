<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder
{
    public function run()
    {
        // reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // permission
        $perms = [
            'manage-all',        // superadmin convenience
            'view-pegawai',
            'create-pegawai',
            'edit-pegawai',
            'delete-pegawai',
            'view-absensi',
            'create-absensi',
            'edit-absensi',
            'delete-absensi',
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // roles
        $rSuper = Role::firstOrCreate(['name' => 'superadmin']);
        $rBend  = Role::firstOrCreate(['name' => 'bendahara']);

        // assign permissions
        $rSuper->givePermissionTo(Permission::all());

        // Bendahara: view pegawai di unit sendiri + manage absensi di unit sendiri
        $rBend->givePermissionTo(['view-pegawai','view-absensi','create-absensi','edit-absensi']);
    }
}