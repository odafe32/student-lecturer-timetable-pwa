<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Support\Facades\Log;

class TestCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the department ID from the logs
        $departmentId = "01970fc2-6613-716e-bf5f-9b55b179b94e"; // This is the department ID from your logs
        
        // Check if the department exists
        $department = Department::find($departmentId);
        
        if (!$department) {
            $this->command->error("Department with ID {$departmentId} not found!");
            return;
        }
        
        $this->command->info("Adding test courses for department: {$department->name}");
        
        // Create test courses for each level
        $levels = ['100', '200', '300', '400'];
        $departmentCode = strtoupper(substr($department->name, 0, 3));
        
        foreach ($levels as $level) {
            // Create two courses per level
            $this->createCourse($departmentId, $departmentCode . $level . '1', "Introduction to {$department->name} {$level}", $level, 'first');
            $this->createCourse($departmentId, $departmentCode . $level . '2', "Advanced {$department->name} {$level}", $level, 'second');
        }
        
        $courseCount = Course::where('department_id', $departmentId)->count();
        $this->command->info("Added {$courseCount} test courses for department {$department->name}");
    }
    
    /**
     * Create a course if it doesn't exist
     */
    private function createCourse($departmentId, $code, $title, $level, $semester)
    {
        $course = Course::updateOrCreate(
            [
                'course_code' => $code,
                'department_id' => $departmentId
            ],
            [
                'course_title' => $title,
                'credit_units' => 3,
                'level' => $level,
                'semester' => $semester,
                'description' => "This is a test course for level {$level}",
                'status' => 'active'
            ]
        );
        
        $this->command->line("Created course: {$code} - {$title}");
        
        return $course;
    }
}