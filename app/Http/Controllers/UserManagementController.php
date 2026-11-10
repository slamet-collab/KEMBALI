<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $actor = Auth::user();
        $isSuperAdmin = $actor->role?->code === 'super_admin';
        $roles = Role::query()
            ->when(!$isSuperAdmin, fn ($query) => $query->where('code', '!=', 'super_admin'))
            ->orderBy('id')
            ->get();
        $users = User::with('role')->latest()->paginate(15);
        $roleUpdateRouteName = $isSuperAdmin
            ? 'superadmin.users.update-role'
            : 'admin.users.update-role';

        return view('admin.users', compact('users', 'roles', 'roleUpdateRouteName', 'isSuperAdmin'));
    }

    public function updateRole(Request $request, User $user)
    {
        $actor = Auth::user();
        $isSuperAdmin = $actor->role?->code === 'super_admin';

        if ($actor->is($user)) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun yang sedang digunakan.');
        }

        if (!$isSuperAdmin && $user->role?->code === 'super_admin') {
            abort(403);
        }

        $validated = $request->validate([
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) use ($isSuperAdmin) {
                    if (!$isSuperAdmin) {
                        $query->where('code', '!=', 'super_admin');
                    }
                }),
            ],
        ], [
            'role_id.required' => 'Pilih role baru untuk pengguna.',
            'role_id.exists' => 'Role yang dipilih tidak valid atau tidak dapat diberikan.',
        ]);

        $newRole = Role::findOrFail($validated['role_id']);
        $previousRole = $user->role?->name ?? 'Tanpa role';

        $user->update(['role_id' => $newRole->id]);

        ActivityLog::create([
            'user_id' => $actor->id,
            'action' => 'UPDATE_USER_ROLE',
            'description' => "Pengguna {$actor->name} mengubah role {$user->name} dari {$previousRole} menjadi {$newRole->name}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$newRole->name}.");
    }
}
