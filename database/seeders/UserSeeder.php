<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'first_login' => true,
        ]);
        User::create([
            'name' => 'Joseph sule Godfrey',
            'email' => 'godfreyj.sule1@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'first_login' => true,
        ]);

        // Create Students
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Student $i",
                'email' => "student$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => now(),
                'first_login' => true,
            ]);
        }

        // Create Lecturers
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => "Lecturer $i",
                'email' => "lecturer$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'lecturer',
                'email_verified_at' => now(),
                'first_login' => true,
            ]);
        }
    }
}