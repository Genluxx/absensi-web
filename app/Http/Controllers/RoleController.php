<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $allPermissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('roles.index', compact('roles', 'allPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $slug = \Illuminate\Support\Str::slug($request->name, '_');

        if (Role::where('slug', $slug)->exists()) {
            return back()->withErrors(['name' => 'Nama role ini sudah dipakai, coba nama lain.']);
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_system' => false,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role baru berhasil dibuat.');
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system) {
            abort(403, 'Role bawaan sistem tidak bisa diubah permission-nya lewat sini.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            abort(403, 'Role bawaan sistem tidak bisa dihapus.');
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['error' => 'Masih ada akun yang memakai role ini, pindahkan dulu akunnya sebelum menghapus role.']);
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}