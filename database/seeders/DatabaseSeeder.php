<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin WeatherTrackPro',
            'email' => 'admin@weathertrack.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Regular User 1
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Regular User 2
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('📧 Admin: admin@weathertrack.com / admin123');
        $this->command->info('📧 User: test@example.com / password');
    }
}
