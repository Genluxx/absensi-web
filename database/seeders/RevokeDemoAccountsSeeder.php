<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RevokeDemoAccountsSeeder extends Seeder
{
    public function run(): void
    {
        User::where(function ($query) {
            $query->where('email', 'like', '%@absensiweb.test')
                ->orWhere('email', 'like', '%@sawita.test');
        })->get()->each(function (User $user) {
            $user->email = 'disabled-demo-'.$user->id.'@example.invalid';
            $user->password = Hash::make(Str::random(64));
            $user->save();
        });
    }
}