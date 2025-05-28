<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we have departments
        $departments = Department::all();
        
        if ($departments->isEmpty()) {
            $this->command->info('No departments found. Please create departments first.');
            return;
        }

        // Sample courses for each department
        foreach ($departments as $department) {
            $departmentCode = strtoupper(substr($department->name, 0, 3));
            
            // Define courses to create
            $coursesToCreate = [
                // 100 Level Courses
                [
                    'course_code' => $departmentCode . '101',
                    'course_title' => 'Introduction to ' . $department->name,
                    'credit_units' => 3,
                    'level' => '100',
                    'semester' => 'first',
                    'description' => 'An introductory course to ' . $department->name,
                ],
                [
                    'course_code' => $departmentCode . '102',
                    'course_title' => 'Fundamentals of ' . $department->name,
                    'credit_units' => 3,
                    'level' => '100',
                    'semester' => 'second',
                    'description' => 'A fundamental course in ' . $department->name,
                ],
                // 200 Level Courses
                [
                    'course_code' => $departmentCode . '201',
                    'course_title' => 'Intermediate ' . $department->name . ' I',
                    'credit_units' => 3,
                    'level' => '200',
                    'semester' => 'first',
                    'description' => 'An intermediate course in ' . $department->name,
                ],
                [
                    'course_code' => $departmentCode . '202',
                    'course_title' => 'Intermediate ' . $department->name . ' II',
                    'credit_units' => 3,
                    'level' => '200',
                    'semester' => 'second',
                    'description' => 'A continuation of Intermediate ' . $department->name . ' I',
                ],
                // 300 Level Courses
                [
                    'course_code' => $departmentCode . '301',
                    'course_title' => 'Advanced ' . $department->name . ' I',
                    'credit_units' => 4,
                    'level' => '300',
                    'semester' => 'first',
                    'description' => 'An advanced course in ' . $department->name,
                ],
                [
                    'course_code' => $departmentCode . '302',
                    'course_title' => 'Advanced ' . $department->name . ' II',
                    'credit_units' => 4,
                    'level' => '300',
                    'semester' => 'second',
                    'description' => 'A continuation of Advanced ' . $department->name . ' I',
                ],
                // 400 Level Courses
                [
                    'course_code' => $departmentCode . '401',
                    'course_title' => $department->name . ' Project I',
                    'credit_units' => 6,
                    'level' => '400',
                    'semester' => 'first',
                    'description' => 'First part of the final year project in ' . $department->name,
                ],
                [
                    'course_code' => $departmentCode . '402',
                    'course_title' => $department->name . ' Project II',
                    'credit_units' => 6,
                    'level' => '400',
                    'semester' => 'second',
                    'description' => 'Second part of the final year project in ' . $department->name,
                ],
            ];

            // Create courses using updateOrCreate to avoid duplicates
            foreach ($coursesToCreate as $courseData) {
                Course::updateOrCreate(
                    [
                        'course_code' => $courseData['course_code']
                    ],
                    [
                        'course_title' => $courseData['course_title'],
                        'credit_units' => $courseData['credit_units'],
                        'department_id' => $department->id,
                        'level' => $courseData['level'],
                        'semester' => $courseData['semester'],
                        'description' => $courseData['description'],
                        'status' => 'active',
                    ]
                );
            }

            $this->command->info("Courses created/updated for department: {$department->name}");
        }

        $totalCourses = Course::count();
        $this->command->info("Course seeding completed! Total courses: {$totalCourses}");
    }
}