<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin WeatherTrackPro',
            'email' => 'admin@weathertrackpro.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'User Demo',
            'email' => 'user@weathertrackpro.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'is_active' => true,
        ]);
    }
}
