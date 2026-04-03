<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Product Owner
        User::factory()
            ->asProductOwner()
            ->create([
                'name' => 'Product Owner',
                'email' => 'po@example.com',
                'password' => Hash::make('password123'),
            ]);

        // Project Manager
        User::factory()
            ->asProjectManager()
            ->create([
                'name' => 'Project Manager',
                'email' => 'pm@example.com',
                'password' => Hash::make('password123'),
            ]);

        // Members
        User::factory()
            ->asMember()
            ->create([
                'name' => 'Developer 1',
                'email' => 'dev1@example.com',
                'password' => Hash::make('password123'),
            ]);

        User::factory()
            ->asMember()
            ->create([
                'name' => 'Developer 2',
                'email' => 'dev2@example.com',
                'password' => Hash::make('password123'),
            ]);

        User::factory()
            ->asMember()
            ->create([
                'name' => 'Developer 3',
                'email' => 'dev3@example.com',
                'password' => Hash::make('password123'),
            ]);
    }
}
