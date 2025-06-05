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

        // Create Students (10 students with real names)
        $studentNames = [
            'John Doe',
            'Jane Smith',
            'Michael Johnson',
            'Emily Williams',
            'David Brown',
            'Sarah Miller',
            'Robert Wilson',
            'Jennifer Taylor',
            'Thomas Anderson',
            'Lisa Jackson'
        ];

        foreach ($studentNames as $index => $name) {
            $emailPrefix = strtolower(str_replace(' ', '.', $name));
            User::create([
                'name' => $name,
                'email' => $emailPrefix . '@example.com',
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
