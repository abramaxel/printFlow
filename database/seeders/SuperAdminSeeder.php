<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Owner Super Admin',
            'email' => 'owner@ordering-system.test',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_SUPER_ADMIN,
            'status' => 'active',
            'email_verified_at' => now(), // Skip verifikasi email
        ]);

        // Opsional: Buat juga akun Admin (Staff Operasional)
        User::create([
            'name' => 'Admin Staff',
            'email' => 'admin@ordering-system.test',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Super Admin & Admin created successfully!');
    }
}