<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'rizkimurfer@gmail.com'],
            [
                'name' => 'Super Administrator',
                'password' => '12345678',
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        User::factory()
            ->member()
            ->count(10)
            ->create();
    }
}