<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@havor.com'],
            [
                'name' => 'Admin Havor',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );

        User::firstOrCreate(
            ['email' => 'editor@havor.com'],
            [
                'name' => 'Editor Havor',
                'password' => Hash::make('password123'),
                'role' => 'editor'
            ]
        );
    }
}
