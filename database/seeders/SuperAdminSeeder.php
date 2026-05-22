<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@karangtaruna.local',
                'password' => Hash::make('superadmin123'),
                'role' => UserRole::SuperAdmin,
                'is_active' => true,
            ]
        );
    }
}
