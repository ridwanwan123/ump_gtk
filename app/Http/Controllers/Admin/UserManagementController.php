<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(10);
        $roles = Role::all();

        // Hitung total global (bukan hanya paginate)
        $totalUsers = User::count();
        $totalSuperadmin = User::role('superadmin')->count();
        $totalOperator = User::role('operator')->count();

        return view('user_management.index', compact(
            'users', 'roles', 'totalUsers', 'totalSuperadmin', 'totalOperator'
        ));
    }


    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Role user berhasil diperbarui!');
    }
}
