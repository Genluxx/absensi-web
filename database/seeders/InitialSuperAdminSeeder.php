<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class InitialSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = config('app.initial_admin');
        $required = ['name', 'username', 'email', 'password'];
        $missing = array_filter($required, fn (string $key) => empty($admin[$key]));

        if ($missing !== []) {
            if (app()->environment('production')) {
                throw new RuntimeException('Set all INITIAL_ADMIN_* environment variables before seeding production.');
            }

            return;
        }

        if (!filter_var($admin['email'], FILTER_VALIDATE_EMAIL) || strlen($admin['password']) < 12) {
            throw new RuntimeException('The initial administrator email is invalid or password is shorter than 12 characters.');
        }

        $existingUser = User::where('username', $admin['username'])->first();
        $isDisabledDemo = $existingUser
            && str_starts_with($existingUser->email, 'disabled-demo-')
            && str_ends_with($existingUser->email, '@example.invalid');

        if ($existingUser && !$isDisabledDemo) {
            if ($existingUser->role !== 'super_admin') {
                throw new RuntimeException('The configured initial administrator username already belongs to another role.');
            }

            return;
        }

        $emailOwner = User::where('email', $admin['email'])->first();

        if ($emailOwner && (!$existingUser || $emailOwner->id !== $existingUser->id)) {
            throw new RuntimeException('The configured initial administrator email is already in use.');
        }

        $role = Role::where('slug', 'super_admin')->firstOrFail();
        $attributes = [
            'name' => $admin['name'],
            'username' => $admin['username'],
            'email' => $admin['email'],
            'password' => Hash::make($admin['password']),
            'role' => 'super_admin',
            'role_id' => $role->id,
            'area' => null,
        ];

        if ($isDisabledDemo) {
            $existingUser->fill($attributes)->save();
            return;
        }

        User::create($attributes);
    }
}