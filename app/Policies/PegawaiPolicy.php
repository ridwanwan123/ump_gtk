<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\User;

class PegawaiPolicy
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

    public function view(User $user, Pegawai $pegawai): bool
    {
        return $user->unit_kerja === $pegawai->id_madrasah;
    }

    public function create(User $user): bool
    {
        return false; // superadmin di handle by before()
    }

    public function update(User $user, Pegawai $pegawai): bool
    {
        return $user->hasRole('operator')
            && $user->unit_kerja === $pegawai->id_madrasah;
    }

    public function delete(User $user, Pegawai $pegawai): bool
    {
        return false; // superadmin via before()
    }
}
