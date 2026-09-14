<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MandorController extends Controller
{
    public function index()
    {
        $mandorList = User::whereNotIn('role', ['super_admin', 'admin_hr'])
            ->orderBy('name')->paginate(10);

        $assignableRoles = Role::whereNotIn('slug', ['super_admin', 'admin_hr'])
            ->orderBy('name')->get();

        return view('mandor.index', compact('mandorList', 'assignableRoles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'area' => 'nullable|string',
        ]);

        $role = Role::findOrFail($request->role_id);

        if (in_array($role->slug, ['super_admin', 'admin_hr'])) {
            abort(403, 'Role ini tidak bisa dibuat lewat form ini.');
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role->slug,
            'role_id' => $role->id,
            'area' => $request->area,
        ]);

        return redirect()->back()->with('success', 'Akun berhasil ditambahkan');
    }

    public function destroy(User $mandor)
    {
        if (in_array($mandor->role, ['super_admin', 'admin_hr'])) {
            abort(403, 'Tidak bisa menghapus akun ini dari sini.');
        }

        if ($mandor->karyawan()->exists()) {
            return redirect()->back()->withErrors(['error' => 'Akun ini masih punya karyawan aktif, tidak bisa dihapus.']);
        }

        if ($mandor->presensi()->exists()) {
            return redirect()->back()->withErrors(['error' => 'Akun ini masih punya riwayat presensi, tidak bisa dihapus.']);
        }

        $mandor->delete();

        return redirect()->back()->with('success', 'Akun berhasil dihapus');
    }
}