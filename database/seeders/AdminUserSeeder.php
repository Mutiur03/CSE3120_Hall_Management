<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@hall.edu'],
            [
                'name' => 'Hall Administrator',
                'password' => Hash::make('admin123'),
                'role' => UserRole::Admin,
                'is_first_login' => false,
                'is_active' => true,
            ]
        );
    }
}
