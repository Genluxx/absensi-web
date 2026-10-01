<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\InitialSuperAdminSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthSeedLoginTest extends TestCase
{
    public function test_super_admin_is_created_from_configured_credentials(): void
    {
        config([
            'app.initial_admin.name' => 'Test Super Admin',
            'app.initial_admin.username' => 'configured-admin',
            'app.initial_admin.email' => 'configured-admin@example.test',
            'app.initial_admin.password' => 'a-strong-test-password',
        ]);

        $this->seed(RolePermissionSeeder::class);
        $this->seed(InitialSuperAdminSeeder::class);

        $user = User::where('username', 'configured-admin')->first();

        $this->assertNotNull($user);
        $this->assertSame('super_admin', $user->role);
        $this->assertTrue(Hash::check('a-strong-test-password', $user->password));
    }
}
