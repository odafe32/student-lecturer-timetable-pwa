<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Support\Str;

class FacultyDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define faculties with their departments
        $facultiesWithDepartments = [
            [
                'name' => 'Faculty of Engineering',
                'code' => 'ENG',
                'description' => 'Faculty of Engineering and Technology',
                'departments' => [
                    ['name' => 'Computer Engineering', 'code' => 'CPE'],
                    ['name' => 'Electrical Engineering', 'code' => 'EEE'],
                    ['name' => 'Mechanical Engineering', 'code' => 'MEE'],
                    ['name' => 'Civil Engineering', 'code' => 'CVE'],
                    ['name' => 'Chemical Engineering', 'code' => 'CHE'],
                ]
            ],
            [
                'name' => 'Faculty of Science',
                'code' => 'SCI',
                'description' => 'Faculty of Science and Technology',
                'departments' => [
                    ['name' => 'Computer Science', 'code' => 'CS'],
                    ['name' => 'Mathematics', 'code' => 'MAT'],
                    ['name' => 'Physics', 'code' => 'PHY'],
                    ['name' => 'Chemistry', 'code' => 'CHM'],
                    ['name' => 'Biology', 'code' => 'BIO'],
                    ['name' => 'Statistics', 'code' => 'STA'],
                ]
            ],
            [
                'name' => 'Faculty of Arts',
                'code' => 'ART',
                'description' => 'Faculty of Arts and Humanities',
                'departments' => [
                    ['name' => 'English Language', 'code' => 'ENG'],
                    ['name' => 'History', 'code' => 'HIS'],
                    ['name' => 'Philosophy', 'code' => 'PHI'],
                    ['name' => 'Fine Arts', 'code' => 'ART'],
                    ['name' => 'Music', 'code' => 'MUS'],
                ]
            ],
            [
                'name' => 'Faculty of Social Sciences',
                'code' => 'SOC',
                'description' => 'Faculty of Social Sciences',
                'departments' => [
                    ['name' => 'Economics', 'code' => 'ECO'],
                    ['name' => 'Political Science', 'code' => 'POL'],
                    ['name' => 'Sociology', 'code' => 'SOC'],
                    ['name' => 'Psychology', 'code' => 'PSY'],
                    ['name' => 'Geography', 'code' => 'GEO'],
                ]
            ],
            [
                'name' => 'Faculty of Medicine',
                'code' => 'MED',
                'description' => 'Faculty of Medicine and Health Sciences',
                'departments' => [
                    ['name' => 'Medicine & Surgery', 'code' => 'MED'],
                    ['name' => 'Nursing', 'code' => 'NUR'],
                    ['name' => 'Pharmacy', 'code' => 'PHM'],
                    ['name' => 'Dentistry', 'code' => 'DEN'],
                ]
            ],
            [
                'name' => 'Faculty of Law',
                'code' => 'LAW',
                'description' => 'Faculty of Law and Legal Studies',
                'departments' => [
                    ['name' => 'Common Law', 'code' => 'LAW'],
                    ['name' => 'Islamic Law', 'code' => 'ISL'],
                ]
            ],
            [
                'name' => 'Faculty of Agriculture',
                'code' => 'AGR',
                'description' => 'Faculty of Agriculture and Agricultural Technology',
                'departments' => [
                    ['name' => 'Crop Production', 'code' => 'CRP'],
                    ['name' => 'Animal Science', 'code' => 'ANS'],
                    ['name' => 'Soil Science', 'code' => 'SOS'],
                    ['name' => 'Agricultural Economics', 'code' => 'AGE'],
                ]
            ],
            [
                'name' => 'Faculty of Education',
                'code' => 'EDU',
                'description' => 'Faculty of Education',
                'departments' => [
                    ['name' => 'Educational Administration', 'code' => 'EDA'],
                    ['name' => 'Curriculum & Instruction', 'code' => 'CIN'],
                    ['name' => 'Guidance & Counseling', 'code' => 'GCO'],
                ]
            ],
        ];

        // Create faculties and their departments
        foreach ($facultiesWithDepartments as $facultyData) {
            $departments = $facultyData['departments'];
            unset($facultyData['departments']);

            $faculty = Faculty::create($facultyData);

            foreach ($departments as $departmentData) {
                $departmentData['faculty_id'] = $faculty->id;
                $departmentData['description'] = 'Department of ' . $departmentData['name'] . ' in ' . $faculty->name;
                Department::create($departmentData);
            }
        }
    }
}