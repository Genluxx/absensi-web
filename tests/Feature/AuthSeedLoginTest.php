<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthSeedLoginTest extends TestCase
{
    public function test_default_super_admin_seed_exists_and_uses_expected_credentials(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::where('username', 'superadmin')->first();

        $this->assertNotNull($user);
        $this->assertSame('super_admin', $user->role);
        $this->assertTrue(Hash::check('super123', $user->password));
    }
}
