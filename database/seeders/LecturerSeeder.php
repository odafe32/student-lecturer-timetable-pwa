<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class LecturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all departments
        $departments = Department::all();
        
        if ($departments->isEmpty()) {
            $this->command->error('No departments found. Please run the FacultyDepartmentSeeder first.');
            return;
        }

        // Create 5 lecturers with unique emails
        $lecturerData = [
            [
                'name' => 'Dr. James Smith',
                'email' => 'james.smith.lecturer@example.com',
                'department_id' => $departments->random()->id,
                'phone_number' => '08012345678',
            ],
            [
                'name' => 'Prof. Sarah Johnson',
                'email' => 'sarah.johnson.lecturer@example.com',
                'department_id' => $departments->random()->id,
                'phone_number' => '08023456789',
            ],
            [
                'name' => 'Dr. Michael Brown',
                'email' => 'michael.brown.lecturer@example.com',
                'department_id' => $departments->random()->id,
                'phone_number' => '08034567890',
            ],
            [
                'name' => 'Dr. Emily Davis',
                'email' => 'emily.davis.lecturer@example.com',
                'department_id' => $departments->random()->id,
                'phone_number' => '08045678901',
            ],
            [
                'name' => 'Prof. Robert Wilson',
                'email' => 'robert.wilson.lecturer@example.com',
                'department_id' => $departments->random()->id,
                'phone_number' => '08056789012',
            ],
        ];

        foreach ($lecturerData as $index => $data) {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'lecturer',
                'first_login' => true,
            ]);

            // Generate staff ID
            $year = date('Y');
            $department = Department::find($data['department_id']);
            $deptCode = $department->code ?? 'DEPT';
            $staffId = $year . '/STAFF/' . $deptCode . '/' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            // Create lecturer profile
            Lecturer::create([
                'user_id' => $user->id,
                'department_id' => $data['department_id'],
                'staff_id' => $staffId,
                'phone_number' => $data['phone_number'],
                'address' => fake()->address(),
                'status' => 'active',
            ]);
            
            $this->command->info("Created lecturer profile for {$data['name']} with staff ID {$staffId}");
        }
    }
}