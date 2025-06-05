<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Timetable;
use App\Models\Lecturer;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TimetableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing timetable data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Timetable::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Creating comprehensive timetable data...');

        // Get all lecturers, faculties, and departments
        $lecturers = Lecturer::with(['department.faculty'])->get();
        $faculties = Faculty::with('departments')->get();
        
        if ($lecturers->isEmpty()) {
            $this->command->warn('No lecturers found. Please run LecturerSeeder first.');
            return;
        }

        // Define time slots
        $timeSlots = [
            ['start' => '08:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '12:00'],
            ['start' => '12:00', 'end' => '14:00'],
            ['start' => '14:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '18:00'],
        ];

        // Define days of the week
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        // Define academic levels
        $levels = ['100', '200', '300', '400', '500'];

        // Define venues
        $venues = [
            'Lecture Hall A', 'Lecture Hall B', 'Lecture Hall C',
            'Room 101', 'Room 102', 'Room 103', 'Room 201', 'Room 202',
            'Lab 1', 'Lab 2', 'Computer Lab', 'Science Lab',
            'Auditorium', 'Conference Room', 'Seminar Room'
        ];

        // Create sample courses for each department
        $this->createSampleCourses($faculties);

        $timetableCount = 0;

        foreach ($lecturers as $lecturer) {
            $this->command->info("Creating timetables for lecturer: {$lecturer->user->name}");
            
            // Create 3-6 timetables per lecturer
            $numTimetables = rand(3, 6);
            
            for ($i = 0; $i < $numTimetables; $i++) {
                $timetable = $this->createTimetableForLecturer($lecturer, $timeSlots, $daysOfWeek, $levels, $venues);
                
                if ($timetable) {
                    $timetableCount++;
                    
                    // Generate session dates and mark some as completed
                    $this->generateSessionsAndMarkCompleted($timetable);
                }
            }
        }

        $this->command->info("Created {$timetableCount} timetable entries with completion tracking.");
    }

    /**
     * Create sample courses for each department.
     */
    private function createSampleCourses($faculties)
    {
        $this->command->info('Creating sample courses...');

        $courseTemplates = [
            'Agriculture' => [
                ['code' => 'AGR101', 'title' => 'Introduction to Agriculture'],
                ['code' => 'AGR201', 'title' => 'Crop Production'],
                ['code' => 'AGR301', 'title' => 'Animal Husbandry'],
                ['code' => 'AGR401', 'title' => 'Agricultural Economics'],
                ['code' => 'AGR501', 'title' => 'Advanced Farming Techniques'],
            ],
            'Engineering' => [
                ['code' => 'ENG101', 'title' => 'Engineering Mathematics'],
                ['code' => 'ENG201', 'title' => 'Mechanics of Materials'],
                ['code' => 'ENG301', 'title' => 'Thermodynamics'],
                ['code' => 'ENG401', 'title' => 'Control Systems'],
                ['code' => 'ENG501', 'title' => 'Advanced Engineering Design'],
            ],
            'Science' => [
                ['code' => 'SCI101', 'title' => 'General Chemistry'],
                ['code' => 'SCI201', 'title' => 'Organic Chemistry'],
                ['code' => 'SCI301', 'title' => 'Physical Chemistry'],
                ['code' => 'SCI401', 'title' => 'Analytical Chemistry'],
                ['code' => 'SCI501', 'title' => 'Advanced Chemical Analysis'],
            ],
            'Arts' => [
                ['code' => 'ART101', 'title' => 'Introduction to Literature'],
                ['code' => 'ART201', 'title' => 'Creative Writing'],
                ['code' => 'ART301', 'title' => 'Literary Criticism'],
                ['code' => 'ART401', 'title' => 'Contemporary Literature'],
                ['code' => 'ART501', 'title' => 'Advanced Literary Studies'],
            ],
            'Business' => [
                ['code' => 'BUS101', 'title' => 'Introduction to Business'],
                ['code' => 'BUS201', 'title' => 'Marketing Principles'],
                ['code' => 'BUS301', 'title' => 'Financial Management'],
                ['code' => 'BUS401', 'title' => 'Strategic Management'],
                ['code' => 'BUS501', 'title' => 'Advanced Business Strategy'],
            ],
        ];

        foreach ($faculties as $faculty) {
            $facultyKey = $this->getFacultyKey($faculty->name);
            $templates = $courseTemplates[$facultyKey] ?? $courseTemplates['Science'];

            foreach ($faculty->departments as $department) {
                // Create a department code prefix to ensure uniqueness
                $deptPrefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $department->name), 0, 3));
                
                foreach ($templates as $template) {
                    $level = substr($template['code'], -3, 1) . '00';
                    
                    // Create a unique course code by combining department prefix and template code
                    $uniqueCourseCode = $deptPrefix . '_' . $template['code'];
                    
                    try {
                        Course::updateOrCreate(
                            [
                                'course_code' => $uniqueCourseCode,
                                'department_id' => $department->id,
                            ],
                            [
                                'course_title' => $template['title'] . ' (' . $department->name . ')',
                                'level' => $level,
                                'semester' => 'both',
                                'credit_units' => rand(2, 4),
                                'status' => 'active',
                            ]
                        );
                    } catch (\Exception $e) {
                        // If still getting duplicate, add a random string
                        $uniqueCourseCode = $deptPrefix . '_' . $template['code'] . '_' . Str::random(3);
                        
                        Course::create([
                            'course_code' => $uniqueCourseCode,
                            'course_title' => $template['title'] . ' (' . $department->name . ')',
                            'department_id' => $department->id,
                            'level' => $level,
                            'semester' => 'both',
                            'credit_units' => rand(2, 4),
                            'status' => 'active',
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Get faculty key for course templates.
     */
    private function getFacultyKey($facultyName)
    {
        $name = strtolower($facultyName);
        
        if (str_contains($name, 'agriculture')) return 'Agriculture';
        if (str_contains($name, 'engineering')) return 'Engineering';
        if (str_contains($name, 'science')) return 'Science';
        if (str_contains($name, 'arts') || str_contains($name, 'humanities')) return 'Arts';
        if (str_contains($name, 'business') || str_contains($name, 'management')) return 'Business';
        
        return 'Science'; // Default
    }

    /**
     * Create a timetable for a specific lecturer.
     */
    private function createTimetableForLecturer($lecturer, $timeSlots, $daysOfWeek, $levels, $venues)
    {
        // Get courses for this lecturer's department
        $courses = Course::where('department_id', $lecturer->department_id)->get();
        
        if ($courses->isEmpty()) {
            return null;
        }

        $course = $courses->random();
        $timeSlot = collect($timeSlots)->random();
        $day = collect($daysOfWeek)->random();
        $level = collect($levels)->random();
        $venue = collect($venues)->random();

        // Generate effective date (some past, some current, some future)
        $effectiveDate = $this->generateEffectiveDate();
        $endDate = $this->generateEndDate($effectiveDate);

        // Check for conflicts
        $hasConflict = Timetable::hasConflict(
            $day,
            $timeSlot['start'],
            $timeSlot['end'],
            $venue,
            $effectiveDate
        );

        if ($hasConflict) {
            // Try a different venue
            $venue = collect($venues)->random();
            $hasConflict = Timetable::hasConflict(
                $day,
                $timeSlot['start'],
                $timeSlot['end'],
                $venue,
                $effectiveDate
            );
            
            if ($hasConflict) {
                return null; // Skip this timetable
            }
        }

        // Determine if this is a recurring class
        $isRecurring = rand(1, 10) <= 8; // 80% chance of being recurring
        $totalSessions = $isRecurring ? rand(10, 16) : 1; // 10-16 sessions for recurring classes

        try {
            // Create the timetable with explicit default values for JSON fields
            $timetable = Timetable::create([
                'lecturer_id' => $lecturer->id,
                'course_id' => $course->id,
                'faculty_id' => $lecturer->department->faculty_id,
                'department_id' => $lecturer->department_id,
                'level' => $level,
                'day_of_week' => $day,
                'start_time' => $timeSlot['start'],
                'end_time' => $timeSlot['end'],
                'venue' => $venue,
                'notes' => $this->generateRandomNotes(),
                'status' => 'active',
                'effective_date' => $effectiveDate,
                'end_date' => $endDate,
                'is_recurring' => $isRecurring,
                'total_sessions' => $totalSessions,
                'completed_sessions' => 0,
                'session_dates' => json_encode([]), // Explicit empty array
                'completed_dates' => json_encode([]), // Explicit empty array
                'completion_status' => 'pending',
            ]);
            
            return $timetable;
        } catch (\Exception $e) {
            $this->command->error("Error creating timetable: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate session dates and mark some as completed.
     */
    private function generateSessionsAndMarkCompleted($timetable)
    {
        // Generate session dates
        $sessionDates = $timetable->generateSessionDates();
        $timetable->update([
            'session_dates' => $sessionDates,
            'total_sessions' => count($sessionDates),
        ]);

        if (empty($sessionDates)) {
            return;
        }

        // Mark some sessions as completed (for past dates and some current dates)
        $today = Carbon::today();
        $completedDates = [];

        foreach ($sessionDates as $sessionDate) {
            $sessionCarbon = Carbon::parse($sessionDate);
            
            // Always mark past sessions as completed
            if ($sessionCarbon->lt($today)) {
                $completedDates[] = $sessionDate;
            }
            // Randomly mark some current/future sessions as completed (to simulate ongoing classes)
            elseif ($sessionCarbon->lte($today->copy()->addWeeks(2)) && rand(1, 10) <= 3) {
                $completedDates[] = $sessionDate;
            }
        }

        // Update completion status
        if (!empty($completedDates)) {
            $completionStatus = 'ongoing';
            if (count($completedDates) >= count($sessionDates)) {
                $completionStatus = 'completed';
            }

            $timetable->update([
                'completed_dates' => $completedDates,
                'completed_sessions' => count($completedDates),
                'completion_status' => $completionStatus,
            ]);
        }
    }

    /**
     * Generate a random effective date.
     */
    private function generateEffectiveDate()
    {
        $scenarios = [
            'past' => 40,      // 40% chance - past dates
            'current' => 35,   // 35% chance - current month
            'future' => 25,    // 25% chance - future dates
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($scenarios as $scenario => $percentage) {
            $cumulative += $percentage;
            if ($random <= $cumulative) {
                switch ($scenario) {
                    case 'past':
                        return Carbon::today()->subMonths(rand(1, 6))->subDays(rand(0, 30));
                    case 'current':
                        return Carbon::today()->subDays(rand(0, 15));
                    case 'future':
                        return Carbon::today()->addDays(rand(1, 60));
                }
            }
        }

        return Carbon::today(); // Fallback
    }

    /**
     * Generate an end date based on effective date.
     */
    private function generateEndDate($effectiveDate)
    {
        $effectiveCarbon = Carbon::parse($effectiveDate);
        
        // 70% chance of having an end date
        if (rand(1, 10) <= 7) {
            return $effectiveCarbon->copy()->addMonths(rand(3, 5))->addDays(rand(0, 15));
        }
        
        return null; // No end date (indefinite)
    }

    /**
     * Generate random notes for timetables.
     */
    private function generateRandomNotes()
    {
        $notes = [
            'Regular weekly class',
            'Practical session included',
            'Bring textbooks and notebooks',
            'Laboratory session',
            'Group project presentation',
            'Midterm examination',
            'Final examination',
            'Guest lecturer session',
            'Field trip planned',
            'Online session available',
            null, // Some timetables have no notes
        ];

        return collect($notes)->random();
    }
}
