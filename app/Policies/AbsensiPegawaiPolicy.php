<?php

namespace App\Policies;

use App\Models\AbsensiPegawai;
use App\Models\User;

class AbsensiPegawaiPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('operator');
    }

    public function view(User $user, AbsensiPegawai $absensi): bool
    {
        return $user->hasRole('operator');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('operator');
    }

    public function update(User $user, AbsensiPegawai $absensi): bool
    {
        return $user->hasRole('operator');
    }

    public function delete(User $user, AbsensiPegawai $absensi): bool
    {
        return $user->hasRole('operator');
    }
}