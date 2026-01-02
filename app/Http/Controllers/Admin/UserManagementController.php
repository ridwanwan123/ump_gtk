<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserManagementController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('roles')->orderBy('name')->paginate(10);
            $roles = Role::all();

            // Hitung total global
            $totalUsers = User::count();
            $totalSuperadmin = User::role('superadmin')->count();
            $totalOperator = User::role('operator')->count();

            // ===== SUKSES =====
            // Log::info('Akses halaman user management', [
            //     'admin_id' => auth()->id(),
            //     'ip' => request()->ip(),
            // ]);

            return view('user_management.index', compact(
                'users', 'roles', 'totalUsers', 'totalSuperadmin', 'totalOperator'
            ));
        } catch (Throwable $e) {
            // ===== ERROR =====
            Log::error('Error membuka halaman user management', [
                'message' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            abort(500, 'Terjadi kesalahan sistem.');
        }
    }

    public function updateRole(Request $request, User $user)
    {
        try {
            $request->validate([
                'role' => 'required|exists:roles,name',
            ]);

            $oldRoles = $user->roles->pluck('name')->toArray();

            $user->syncRoles([$request->role]);

            // ===== SUKSES =====
            Log::info('Role user berhasil diperbarui', [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'old_roles' => $oldRoles,
                'new_role' => $request->role,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('admin.users.index')
                ->with('swal_success', 'Role user berhasil diperbarui!');
        } catch (ValidationException $e) {
            // Validasi gagal → biarkan Laravel handle
            throw $e;
        } catch (Throwable $e) {
            // ===== ERROR =====
            Log::error('Gagal memperbarui role user', [
                'admin_id' => auth()->id(),
                'user_id' => $user->id ?? null,
                'message' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return redirect()->back()
                ->with('swal_error', 'Gagal memperbarui role user. Silakan coba lagi.');
        }
    }
}
