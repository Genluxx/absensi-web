<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->withCount(['users', 'permissions'])->orderBy('name')->get();
        $allPermissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('roles.index', compact('roles', 'allPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|distinct|exists:permissions,id',
        ]);

        $slug = \Illuminate\Support\Str::slug($request->name, '_');

        if (Role::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'Nama role ini sudah dipakai, coba nama lain.']);
        }

        $role = DB::transaction(function () use ($request, $slug) {
            $role = Role::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'is_system' => false,
            ]);

            $role->permissions()->sync(array_values(array_unique($request->input('permissions', []))));

            return $role;
        });

        return redirect()->route('roles.index')->with('success', 'Role baru berhasil dibuat.');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|distinct|exists:permissions,id',
        ]);

        $permissionIds = array_values(array_unique($request->input('permissions', [])));

        DB::transaction(function () use ($request, $role, $permissionIds) {
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $role->permissions()->sync($permissionIds);

            $role->users()->update(['role' => $role->slug]);
            User::where('role', $role->slug)->update(['role_id' => $role->id]);
        });

        AuditLog::record('sync_role_permissions', 'Menyinkronkan permission untuk role '.$role->name.'.', $role, [
            'permission_ids' => $permissionIds,
            'permission_count' => count($permissionIds),
        ]);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            abort(403, 'Role bawaan sistem tidak bisa dihapus.');
        }

        if ($role->users()->exists() || User::where('role', $role->slug)->exists()) {
            return back()->withErrors(['error' => 'Masih ada akun yang memakai role ini, pindahkan dulu akunnya sebelum menghapus role.']);
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}