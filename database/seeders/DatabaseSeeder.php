<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'superadmin', 'description' => 'Super Administrator with full access'],
            ['name' => 'admin', 'description' => 'Administrator with limited access'],
            ['name' => 'player', 'description' => 'Cricket player'],
            ['name' => 'umpire', 'description' => 'Match umpire'],
            ['name' => 'scorer', 'description' => 'Match scorer'],
            ['name' => 'commentator', 'description' => 'Match commentator'],
            ['name' => 'organiser', 'description' => 'Tournament organiser'],
            ['name' => 'manager', 'description' => 'Team manager'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create super admin user
        $superAdmin = User::create([
            'user_id' => 'USR00001',
            'name' => 'Super Admin',
            'email' => 'sportavani@gmail.com',
            'mobile' => '9876543210',
            'password' => Hash::make('12345678'),
            'city' => 'Mumbai',
        ]);

        // Assign super admin role
        $superAdmin->roles()->attach(Role::where('name', 'superadmin')->first()->id);
    }
}

