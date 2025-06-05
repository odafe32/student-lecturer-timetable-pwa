<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all departments with their faculty relationships
        $departments = Department::with('faculty')->get();
        
        if ($departments->isEmpty()) {
            $this->command->error('No departments found. Please run the FacultyDepartmentSeeder first.');
            return;
        }
        
        // Get existing student users
        $studentUsers = User::where('role', 'student')->get();
        
        if ($studentUsers->isEmpty()) {
            $this->command->error('No student users found. Please run the UserSeeder first.');
            return;
        }
        
        // Track student numbers per department for sequential numbering
        $departmentStudentCounts = [];
        
        // Current year for matric number
        $currentYear = date('Y');
        
        // Available levels for students
        $levels = [100, 200, 300, 400];
        
        // Assign each student user to a department with a matric number
        foreach ($studentUsers as $index => $user) {
            // Select a department (cycle through available departments)
            $department = $departments[$index % count($departments)];
            
            // Get faculty code from the department's faculty
            $facultyCode = $department->faculty->code;
            $departmentCode = $department->code;
            
            // Initialize counter for this department if not exists
            if (!isset($departmentStudentCounts[$departmentCode])) {
                $departmentStudentCounts[$departmentCode] = 1;
            } else {
                $departmentStudentCounts[$departmentCode]++;
            }
            
            // Generate sequential number for this department
            $sequentialNumber = str_pad($departmentStudentCounts[$departmentCode], 4, '0', STR_PAD_LEFT);
            
            // Generate matric number in format: year/faculty/department/no
            $matricNumber = "{$currentYear}/{$facultyCode}/{$departmentCode}/{$sequentialNumber}";
            
            // Select a level (cycle through available levels)
            $level = $levels[$index % count($levels)];
            
            // Create student profile
            Student::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'department_id' => $department->id,
                'matric_number' => $matricNumber,
                'level' => $level,
                'status' => 'active',
                'address' => $user->name . "'s Address, University Campus",
            ]);
            
            $this->command->info("Created student profile for {$user->name} with matric number {$matricNumber}");
        }
    }
}
