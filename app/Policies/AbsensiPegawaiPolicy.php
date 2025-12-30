<?php

namespace App\Policies;

use App\Models\AbsensiPegawai;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsensiPegawaiPolicy
{
    public function before(User $user)
    {
        // Superadmin bisa melakukan semua aksi
        if ($user->hasRole('superadmin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Superadmin dan bendahara bisa melihat daftar absensi pegawai
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->hasRole('bendahara');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        // Superadmin dan bendahara bisa melihat absensi pegawai
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->hasRole('bendahara');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya bendahara yang bisa membuat absensi
        return $user->hasRole('bendahara');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        // Hanya bendahara yang bisa mengupdate absensi
        return $user->hasRole('bendahara');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        // Hanya bendahara yang bisa menghapus absensi
        return $user->hasRole('bendahara');
    }

    public function restore(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return false;  // Tidak ada yang bisa restore absensi
    }

    public function forceDelete(User $user, AbsensiPegawai $absensiPegawai): bool
    {
        return false;  // Tidak ada yang bisa force delete absensi
    }
}
