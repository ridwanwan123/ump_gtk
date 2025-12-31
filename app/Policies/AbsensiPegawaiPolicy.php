<?php

namespace App\Policies;

use App\Models\AbsensiPegawai;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsensiPegawaiPolicy
{
    /**
     * Superadmin & bendahara boleh lihat data
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['superadmin', 'bendahara']);
    }

    public function view(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return $user->hasRole(['superadmin', 'bendahara']);
    }

    /**
     * HANYA bendahara boleh create
     */
    public function create(User $user): bool
    {
        return $user->hasRole('bendahara');
    }

    /**
     * HANYA bendahara boleh update
     */
    public function update(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return $user->hasRole('bendahara');
    }

    /**
     * HANYA bendahara boleh delete
     */
    public function delete(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return $user->hasRole('bendahara');
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
