<?php

namespace App\Policies;

use App\Models\AbsensiPegawai;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsensiPegawaiPolicy
{
    public function before(User $user)
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('bendahara');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return $user->unit_kerja === $absensi->pegawai->id_madrasah;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return $user->unit_kerja === $absensi->pegawai->id_madrasah;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        //
    }
}
