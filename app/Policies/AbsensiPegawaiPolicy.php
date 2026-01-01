<?php

namespace App\Policies;

use App\Models\AbsensiPegawai;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsensiPegawaiPolicy
{
    /**
     * Superadmin & operator boleh lihat data
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['superadmin', 'operator']);
    }

    public function view(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return $user->hasRole(['superadmin', 'operator']);
    }

    /**
     * HANYA operator boleh create
     */
    public function create(User $user): bool
    {
        return $user->hasRole('operator');
    }

    /**
     * HANYA operator boleh update
     */
    public function update(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return $user->hasRole('operator');
    }

    /**
     * HANYA operator boleh delete
     */
    public function delete(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return $user->hasRole('operator');
    }

    public function restore(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return false;
    }

    public function forceDelete(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return false;
    }
}
