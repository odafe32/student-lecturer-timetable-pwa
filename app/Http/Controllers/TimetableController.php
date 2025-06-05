<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Timetable;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    /**
     * Display the timetable management page with completion tracking.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    // Replace the index method completely
    public function index(Request $request)
    {
        $lecturer = Auth::user()->lecturerProfile;
        
        // Get filter parameters
        $filter = $request->get('filter', 'current');
        $month = $request->get('month');
        $year = $request->get('year', date('Y'));
        
        // Check if timetables table exists
        if (Schema::hasTable('timetables')) {
            // Base query
            $query = Timetable::with(['course', 'faculty', 'department'])
                ->where('lecturer_id', $lecturer->id);
                
            // Apply filters
            switch ($filter) {
                case 'current':
                    $query->active()->currentWeek();
                    break;
                
                case 'past':
                    $query->where(function($q) {
                        $q->where('end_date', '<', Carbon::today())
                          ->orWhere('completion_status', 'completed');
                    });
                    break;
                case 'completed':
                    $query->where('completion_status', 'completed');
                    break;
                case 'ongoing':
                    $query->where('completion_status', 'ongoing');
                    break;
                case 'pending':
                    $query->where('completion_status', 'pending');
                    break;
                case 'all':
                    // No additional filters
                    break;
            }
            
            // Apply month/year filter if provided
            if ($month && $year) {
                $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();
                
                $query->where(function($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('effective_date', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                      ->orWhere(function($subQ) use ($startOfMonth, $endOfMonth) {
                          $subQ->where('effective_date', '<=', $startOfMonth)
                               ->where(function($subSubQ) use ($endOfMonth) {
                                   $subSubQ->where('end_date', '>=', $endOfMonth)
                                           ->orWhereNull('end_date');
                               });
                      });
                });
            }
            
            $timetables = $query->orderBy('effective_date', 'desc')
                               ->orderBy('day_of_week')
                               ->orderBy('start_time')
                               ->get();
            
            // Update session dates for all timetables that need it
            foreach ($timetables as $timetable) {
                if ($timetable->is_recurring && (!$timetable->session_dates || empty($timetable->session_dates))) {
                    $timetable->updateSessionDates();
                    $timetable->refresh(); // Refresh to get updated data
                }
            }
            
            // Group timetables by different criteria based on filter
            if ($filter === 'current') {
                $weeklySchedule = $this->groupTimetablesByCurrentWeek($timetables);
                $groupedTimetables = null;
            } else {
                $weeklySchedule = null;
                $groupedTimetables = $this->groupTimetablesByStatus($timetables);
            }
            
            // Get statistics
            $stats = $this->getTimetableStatistics($lecturer->id);
            
        } else {
            $weeklySchedule = $this->emptyWeeklySchedule();
            $groupedTimetables = null;
            $stats = [];
        }
        
        // Get faculties for dropdown
        if (Schema::hasColumn('faculties', 'status')) {
            $faculties = Faculty::active()->get();
        } else {
            $faculties = Faculty::all();
        }
        
        return view('lecturer.time-table', [
            'title' => 'Timetable Management - Affan Student Timetable',
            'description' => 'Manage your class schedules and timetables',
            'ogImage' => url('images/icons/favicon.png'),
            'weeklySchedule' => $weeklySchedule,
            'groupedTimetables' => $groupedTimetables,
            'faculties' => $faculties,
            'lecturer' => $lecturer,
            'currentFilter' => $filter,
            'currentMonth' => $month,
            'currentYear' => $year,
            'stats' => $stats,
        ]);
    }
    
    /**
     * Show the form for creating a new timetable entry.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $lecturer = Auth::user()->lecturerProfile;

        // Get faculties for dropdown - check if status column exists
        if (Schema::hasColumn('faculties', 'status')) {
            $faculties = Faculty::active()->get();
        } else {
            $faculties = Faculty::all();
        }

        $departments = collect();
        $selectedFaculty = null;

        // Check if faculty_id is provided in the request
        if (request('faculty_id')) {
            // Get the selected faculty
            $selectedFaculty = Faculty::find(request('faculty_id'));

            if ($selectedFaculty) {
                // Get departments for the selected faculty
                if (Schema::hasColumn('departments', 'status')) {
                    $departments = Department::where('faculty_id', request('faculty_id'))
                        ->active()
                        ->get();
                } else {
                    $departments = Department::where('faculty_id', request('faculty_id'))
                        ->get();
                }
            }
        } else {
            // If lecturer belongs to a department, get related data (fallback)
            if ($lecturer->department_id) {
                if (Schema::hasColumn('departments', 'status')) {
                    $departments = Department::where('faculty_id', $lecturer->department->faculty_id)
                        ->active()
                        ->get();
                } else {
                    $departments = Department::where('faculty_id', $lecturer->department->faculty_id)
                        ->get();
                }
            }
        }

        return view('lecturer.create-timetable', [
            'title' => 'Create Timetable - Affan Student Timetable',
            'description' => 'Create a new class schedule',
            'ogImage' => url('images/icons/favicon.png'),
            'faculties' => $faculties,
            'departments' => $departments,
            'selectedFaculty' => $selectedFaculty,
            'lecturer' => $lecturer,
        ]);
    }
    
    /**
     * Store a newly created timetable entry with completion tracking.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Check if necessary tables exist
        if (!Schema::hasTable('timetables')) {
            return redirect()->route('lecturer.time-table')
                ->with('error', 'The timetable system is not fully set up yet. Please contact the administrator.');
        }
        
        $lecturer = Auth::user()->lecturerProfile;
        
        $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|string',
            'course_code' => 'required|string|max:20',
            'course_title' => 'required|string|max:255',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'effective_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:effective_date',
            'is_recurring' => 'boolean',
            'total_sessions' => 'nullable|integer|min:1',
        ]);
        
        // Check for scheduling conflicts
        $hasConflict = Timetable::hasConflict(
            $request->day_of_week,
            $request->start_time,
            $request->end_time,
            $request->venue,
            $request->effective_date
        );
        
        if ($hasConflict) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['conflict' => 'There is already a class scheduled at this time and venue. Please choose a different time or venue.']);
        }
        
        // Find or create the course
        $course = $this->findOrCreateCourse(
            $request->course_code,
            $request->course_title,
            $request->department_id,
            $request->level
        );
        
        // Create the timetable entry
        $timetable = Timetable::create([
            'lecturer_id' => $lecturer->id,
            'course_id' => $course->id,
            'faculty_id' => $request->faculty_id,
            'department_id' => $request->department_id,
            'level' => $request->level,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'venue' => $request->venue,
            'notes' => $request->notes,
            'effective_date' => $request->effective_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'is_recurring' => $request->boolean('is_recurring', true),
            'total_sessions' => $request->total_sessions,
            'completion_status' => 'pending',
        ]);
        
        // Generate session dates if recurring
        if (isset($timetable->is_recurring) && $timetable->is_recurring) {
            $timetable->updateSessionDates();
        }
        
        return redirect()->route('lecturer.time-table')
            ->with('success', 'Timetable entry created successfully!');
    }
    
    /**
     * Show the form for editing a timetable entry.
     *
     * @param \App\Models\Timetable $timetable
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Timetable $timetable)
    {
        $lecturer = Auth::user()->lecturerProfile;

        // Ensure lecturer can only edit their own timetables
        if ($timetable->lecturer_id !== $lecturer->id) {
            abort(403, 'Unauthorized action.');
        }

        // Get faculties for dropdown - check if status column exists
        if (Schema::hasColumn('faculties', 'status')) {
            $faculties = Faculty::active()->get();
        } else {
            $faculties = Faculty::all();
        }

        $departments = collect();
        $selectedFaculty = null;

        // Check if faculty_id is provided in the request (for changing faculty)
        if (request('faculty_id')) {
            // Get the selected faculty from request
            $selectedFaculty = Faculty::find(request('faculty_id'));

            if ($selectedFaculty) {
                // Get departments for the selected faculty
                if (Schema::hasColumn('departments', 'status')) {
                    $departments = Department::where('faculty_id', request('faculty_id'))
                        ->active()
                        ->get();
                } else {
                    $departments = Department::where('faculty_id', request('faculty_id'))
                        ->get();
                }
            }
        } else {
            // Use the timetable's current faculty
            $selectedFaculty = $timetable->faculty;

            // Get departments based on timetable's current faculty
            if (Schema::hasColumn('departments', 'status')) {
                $departments = Department::where('faculty_id', $timetable->faculty_id)
                    ->active()
                    ->get();
            } else {
                $departments = Department::where('faculty_id', $timetable->faculty_id)
                    ->get();
            }
        }

        return view('lecturer.edit-timetable', [
            'title' => 'Edit Timetable - Affan Student Timetable',
            'description' => 'Edit class schedule',
            'ogImage' => url('images/icons/favicon.png'),
            'timetable' => $timetable,
            'faculties' => $faculties,
            'departments' => $departments,
            'selectedFaculty' => $selectedFaculty,
            'lecturer' => $lecturer,
        ]);
    }
    
    /**
     * Update the specified timetable entry.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Timetable $timetable
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Timetable $timetable)
    {
        $lecturer = Auth::user()->lecturerProfile;
        
        // Ensure lecturer can only update their own timetables
        if ($timetable->lecturer_id !== $lecturer->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|string',
            'course_code' => 'required|string|max:20',
            'course_title' => 'required|string|max:255',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'effective_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:effective_date',
            'status' => 'required|in:active,cancelled,rescheduled',
        ]);
        
        // Check for scheduling conflicts (excluding current timetable)
        $hasConflict = Timetable::hasConflict(
            $request->day_of_week,
            $request->start_time,
            $request->end_time,
            $request->venue,
            $request->effective_date,
            $timetable->id
        );
        
        if ($hasConflict) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['conflict' => 'There is already a class scheduled at this time and venue. Please choose a different time or venue.']);
        }
        
        // Find or create the course
        $course = $this->findOrCreateCourse(
            $request->course_code,
            $request->course_title,
            $request->department_id,
            $request->level
        );
        
        // Update the timetable entry
        $timetable->update([
            'course_id' => $course->id,
            'faculty_id' => $request->faculty_id,
            'department_id' => $request->department_id,
            'level' => $request->level,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'venue' => $request->venue,
            'notes' => $request->notes,
            'effective_date' => $request->effective_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);
        
        return redirect()->route('lecturer.time-table')
            ->with('success', 'Timetable entry updated successfully!');
    }
    
    /**
     * Remove the specified timetable entry.
     *
     * @param \App\Models\Timetable $timetable
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Timetable $timetable)
    {
        $lecturer = Auth::user()->lecturerProfile;
        
        // Ensure lecturer can only delete their own timetables
        if ($timetable->lecturer_id !== $lecturer->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $timetable->delete();
        
        return redirect()->route('lecturer.time-table')
            ->with('success', 'Timetable entry deleted successfully!');
    }
    
    /**
     * Get departments by faculty (AJAX).
     *
     * @param int $faculty_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepartmentsByFaculty($faculty_id)
    {
        try {
            // Debug logging
            \Log::info('Getting departments for faculty', ['faculty_id' => $faculty_id]);

            // Validate faculty_id
            if (!$faculty_id) {
                \Log::warning('No faculty ID provided');
                return response()->json(['error' => 'Faculty ID is required'], 400);
            }

            // Check if faculty exists
            $faculty = Faculty::find($faculty_id);
            if (!$faculty) {
                \Log::warning('Faculty not found', ['faculty_id' => $faculty_id]);
                return response()->json(['error' => 'Faculty not found'], 404);
            }

            \Log::info('Faculty found', ['faculty_name' => $faculty->name]);

            // Get departments
            $query = Department::where('faculty_id', $faculty_id);

            if (Schema::hasColumn('departments', 'status')) {
                $query->where('status', 'active');
            }

            $departments = $query->orderBy('name')->get();

            \Log::info('Departments found', [
                'count' => $departments->count(),
                'departments' => $departments->pluck('name')->toArray()
            ]);

            return response()->json($departments);

        } catch (\Exception $e) {
            \Log::error('Error getting departments', [
                'faculty_id' => $faculty_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to load departments: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get courses by department (AJAX).
     *
     * @param int $department_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCoursesByDepartment($department_id)
    {
        try {
            // Check if courses table exists
            if (!Schema::hasTable('courses')) {
                return response()->json([]);
            }
            
            $query = Course::where('department_id', $department_id);
            
            // Apply level filter if provided
            if (request()->has('level') && request('level')) {
                $query->where('level', request('level'));
            }
            
            // Apply status filter if column exists
            if (Schema::hasColumn('courses', 'status')) {
                $query->where('status', 'active');
            }
            
            $courses = $query->orderBy('course_code')->get();
            
            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load courses: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Create an empty weekly schedule.
     *
     * @return array
     */
    private function emptyWeeklySchedule()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $schedule = [];

        foreach ($days as $day) {
            $schedule[$day] = collect();
        }

        return $schedule;
    }

    // Add this new method to properly group timetables for current week
    private function groupTimetablesByCurrentWeek($timetables)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $schedule = [];
        
        // Initialize empty collections for each day
        foreach ($days as $day) {
            $schedule[$day] = collect();
        }
        
        // Get current week date range
        $startOfWeek = Carbon::now()->startOfWeek(); // Monday
        $endOfWeek = Carbon::now()->endOfWeek(); // Sunday
        
        foreach ($timetables as $timetable) {
            // Check if this timetable has a session this week
            if ($this->hasSessionThisWeek($timetable, $startOfWeek, $endOfWeek)) {
                $dayOfWeek = $timetable->day_of_week;
                if (isset($schedule[$dayOfWeek])) {
                    $schedule[$dayOfWeek]->push($timetable);
                }
            }
        }
        
        return $schedule;
    }

    // Add this method to check if a timetable has a session for current week
    private function hasSessionThisWeek($timetable, $startOfWeek, $endOfWeek)
    {
        // Check if timetable is active
        if ($timetable->status !== 'active') {
            return false;
        }
        
        // If no effective date is set, assume it starts immediately
        $effectiveDate = $timetable->effective_date ? Carbon::parse($timetable->effective_date) : Carbon::now()->subDays(1);
        
        // Check if effective date is after this week's end
        if ($effectiveDate->gt($endOfWeek)) {
            return false; // Starts after this week
        }
        
        // Check if end date is before this week's start
        if ($timetable->end_date) {
            $endDate = Carbon::parse($timetable->end_date);
            if ($endDate->lt($startOfWeek)) {
                return false; // Ended before this week
            }
        }
        
        // For non-recurring classes
        if (!$timetable->is_recurring) {
            return $effectiveDate->between($startOfWeek, $endOfWeek);
        }
        
        // For recurring classes - check if the day of week falls in this week
        $dayOfWeekMap = [
            'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 
            'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 0,
        ];
        
        $targetDayOfWeek = $dayOfWeekMap[strtolower($timetable->day_of_week)] ?? 1;
        
        // Check if the target day falls within this week and after effective date
        $currentDate = $startOfWeek->copy();
        while ($currentDate->lte($endOfWeek)) {
            if ($currentDate->dayOfWeek === $targetDayOfWeek && $currentDate->gte($effectiveDate)) {
                return true;
            }
            $currentDate->addDay();
        }
        
        return false;
    }
    // Update the existing groupTimetablesByDay method (keep as backup)
    private function groupTimetablesByDay($timetables)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $schedule = [];
        
        foreach ($days as $day) {
            $schedule[$day] = $timetables->where('day_of_week', $day)->values();
        }
        
        return $schedule;
    }

    /**
     * Get weekly schedule for a specific month and year.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMonthlyWeeklySchedule(Request $request)
    {
        $lecturer = Auth::user()->lecturerProfile;
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        try {
            // Get all active timetables for the lecturer
            $timetables = Timetable::with(['course', 'faculty', 'department'])
                ->where('lecturer_id', $lecturer->id)
                ->active()
                ->get();

            // Update session dates for recurring timetables
            foreach ($timetables as $timetable) {
                if ($timetable->is_recurring && (!$timetable->session_dates || empty($timetable->session_dates))) {
                    $timetable->updateSessionDates();
                }
            }

            // Get all weeks in the specified month
            $weeks = $this->getWeeksInMonth($month, $year, $timetables);

            return response()->json([
                'success' => true,
                'weeks' => $weeks,
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::createFromDate($year, $month, 1)->format('F Y')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load monthly schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all weeks in a specific month with their schedules.
     *
     * @param int $month
     * @param int $year
     * @param \Illuminate\Database\Eloquent\Collection $timetables
     * @return array
     */
    private function getWeeksInMonth($month, $year, $timetables)
    {
        $weeks = [];

        // Get the first day of the month
        $firstDayOfMonth = Carbon::createFromDate($year, $month, 1);

        // Get the first Monday of the month (or the Monday before if month doesn't start on Monday)
        $firstMonday = $firstDayOfMonth->copy()->startOfWeek();

        // Get the last day of the month
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

        // Get the last Sunday of the month (or the Sunday after if month doesn't end on Sunday)
        $lastSunday = $lastDayOfMonth->copy()->endOfWeek();

        $currentWeek = $firstMonday->copy();
        $weekNumber = 1;

        // Generate all weeks that overlap with the month
        while ($currentWeek->lte($lastSunday)) {
            $weekStart = $currentWeek->copy()->startOfWeek();
            $weekEnd = $currentWeek->copy()->endOfWeek();

            // Check if this week overlaps with the target month
            $weekOverlapsMonth = (
                $weekStart->month == $month ||
                $weekEnd->month == $month ||
                ($weekStart->lt($firstDayOfMonth) && $weekEnd->gt($lastDayOfMonth))
            );

            if ($weekOverlapsMonth) {
                $weekSchedule = $this->getWeekSchedule($weekStart, $weekEnd, $timetables);

                // Check if this week has any classes
                $hasClasses = false;
                foreach ($weekSchedule as $daySchedule) {
                    if ($daySchedule->count() > 0) {
                        $hasClasses = true;
                        break;
                    }
                }

                $weeks[] = [
                    'week_number' => $weekNumber,
                    'start_date' => $weekStart->format('Y-m-d'),
                    'end_date' => $weekEnd->format('Y-m-d'),
                    'week_label' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d, Y'),
                    'is_current_week' => $this->isCurrentWeek($weekStart, $weekEnd),
                    'has_classes' => $hasClasses,
                    'schedule' => $weekSchedule,
                ];
            }

            $currentWeek->addWeek();
            $weekNumber++;

            // Safety check to prevent infinite loop
            if ($weekNumber > 10) {
                break;
            }
        }

        return $weeks;
    }

    /**
     * Get schedule for a specific week.
     *
     * @param Carbon $weekStart
     * @param Carbon $weekEnd
     * @param \Illuminate\Database\Eloquent\Collection $timetables
     * @return array
     */
    private function getWeekSchedule($weekStart, $weekEnd, $timetables)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $weekSchedule = [];

        foreach ($days as $day) {
            $weekSchedule[$day] = collect();
        }

        foreach ($timetables as $timetable) {
            if ($this->hasSessionThisWeek($timetable, $weekStart, $weekEnd)) {
                $dayOfWeek = $timetable->day_of_week;
                if (isset($weekSchedule[$dayOfWeek])) {
                    $weekSchedule[$dayOfWeek]->push($timetable);
                }
            }
        }

        return $weekSchedule;
    }

    /**
     * Check if a week is the current week.
     *
     * @param Carbon $weekStart
     * @param Carbon $weekEnd
     * @return bool
     */
    private function isCurrentWeek($weekStart, $weekEnd)
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        return $weekStart->isSameDay($currentWeekStart) && $weekEnd->isSameDay($currentWeekEnd);
    }

    /**
     * Group timetables by completion status.
     *
     * @param \Illuminate\Database\Eloquent\Collection $timetables
     * @return array
     */
    private function groupTimetablesByStatus($timetables)
    {
        return [
            'completed' => $timetables->where('completion_status', 'completed')->values(),
            'ongoing' => $timetables->where('completion_status', 'ongoing')->values(),
            'pending' => $timetables->where('completion_status', 'pending')->values(),
            'cancelled' => $timetables->where('status', 'cancelled')->values(),
        ];
    }

    /**
     * Get timetable statistics for the lecturer.
     *
     * @param string $lecturerId
     * @return array
     */
    private function getTimetableStatistics($lecturerId)
    {
        $stats = [];
        
        // Total timetables
        $stats['total'] = Timetable::where('lecturer_id', $lecturerId)->count();
        
        // Completed timetables
        $stats['completed'] = Timetable::where('lecturer_id', $lecturerId)
            ->where('completion_status', 'completed')
            ->count();
        
        // Ongoing timetables
        $stats['ongoing'] = Timetable::where('lecturer_id', $lecturerId)
            ->where('completion_status', 'ongoing')
            ->count();
        
        // Pending timetables
        $stats['pending'] = Timetable::where('lecturer_id', $lecturerId)
            ->where('completion_status', 'pending')
            ->count();
        
        // Total sessions completed
        $stats['total_sessions_completed'] = Timetable::where('lecturer_id', $lecturerId)
            ->sum('completed_sessions');
        
        // Total sessions planned
        $stats['total_sessions_planned'] = Timetable::where('lecturer_id', $lecturerId)
            ->sum('total_sessions');
        
        // Overall completion percentage
        $stats['overall_completion_percentage'] = $stats['total_sessions_planned'] > 0 
            ? round(($stats['total_sessions_completed'] / $stats['total_sessions_planned']) * 100, 2)
            : 0;
        
        // Current week active classes
        $stats['current_week_active'] = Timetable::where('lecturer_id', $lecturerId)
            ->active()
            ->currentWeek()
            ->count();
        
        return $stats;
    }

    /**
     * Mark a session as completed.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Timetable $timetable
     * @return \Illuminate\Http\JsonResponse
     */
    public function markSessionCompleted(Request $request, Timetable $timetable)
    {
        $lecturer = Auth::user()->lecturerProfile;
        
        // Ensure lecturer can only mark their own sessions
        if ($timetable->lecturer_id !== $lecturer->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        
        $request->validate([
            'date' => 'required|date'
        ]);
        
        try {
            $timetable->markSessionCompleted($request->date);
            
            return response()->json([
                'success' => true,
                'message' => 'Session marked as completed',
                'completion_percentage' => $timetable->getCompletionPercentage(),
                'completed_sessions' => $timetable->completed_sessions,
                'total_sessions' => $timetable->total_sessions
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to mark session as completed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get timetable details with sessions.
     *
     * @param \App\Models\Timetable $timetable
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimetableDetails(Timetable $timetable)
    {
        $lecturer = Auth::user()->lecturerProfile;
        
        // Ensure lecturer can only view their own timetables
        if ($timetable->lecturer_id !== $lecturer->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        
        try {
            $timetable->load(['course', 'faculty', 'department']);
            
            return response()->json([
                'timetable' => $timetable,
                'past_sessions' => $timetable->getPastSessions(),
                'upcoming_sessions' => $timetable->getUpcomingSessions(),
                'completion_percentage' => $timetable->getCompletionPercentage(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load timetable details: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export timetable to PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        $lecturer = Auth::user()->lecturerProfile;
        
        $timetables = Timetable::with(['course', 'faculty', 'department'])
            ->where('lecturer_id', $lecturer->id)
            ->active()
            ->currentWeek()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
            
        $weeklySchedule = $this->groupTimetablesByDay($timetables);
        
        // You can use a PDF library like DomPDF or TCPDF here
        // For now, returning a view that can be printed
        return view('lecturer.timetable-pdf', [
            'weeklySchedule' => $weeklySchedule,
            'lecturer' => $lecturer,
        ]);
    }
    
    /**
     * Find or create a course based on the provided details.
     *
     * @param string $courseCode
     * @param string $courseTitle
     * @param string $departmentId
     * @param string $level
     * @return \App\Models\Course
     */
    private function findOrCreateCourse($courseCode, $courseTitle, $departmentId, $level)
    {
        try {
            DB::beginTransaction();

            // First, try to find the course by course_code only (regardless of department)
            $existingCourse = Course::where('course_code', $courseCode)->first();

            if ($existingCourse) {
                // Course code already exists
                if ($existingCourse->department_id == $departmentId) {
                    // Same department - just update the title if needed
                    if ($existingCourse->course_title !== $courseTitle) {
                        $existingCourse->update(['course_title' => $courseTitle]);
                    }
                    DB::commit();
                    return $existingCourse;
                } else {
                    // Different department - this is a conflict
                    // We need to make the course code unique by adding department prefix
                    $department = Department::find($departmentId);
                    $departmentCode = strtoupper(substr($department->name, 0, 3));
                    $newCourseCode = $departmentCode . '_' . $courseCode;

                    // Check if this new course code exists
                    $courseWithNewCode = Course::where('course_code', $newCourseCode)
                        ->where('department_id', $departmentId)
                        ->first();

                    if ($courseWithNewCode) {
                        // Update existing course with new code
                        if ($courseWithNewCode->course_title !== $courseTitle) {
                            $courseWithNewCode->update(['course_title' => $courseTitle]);
                        }
                        DB::commit();
                        return $courseWithNewCode;
                    } else {
                        // Create new course with department-prefixed code
                        $course = Course::create([
                            'course_code' => $newCourseCode,
                            'course_title' => $courseTitle,
                            'department_id' => $departmentId,
                            'level' => $level,
                            'semester' => 'both',
                            'credit_units' => 3,
                            'status' => 'active',
                        ]);
                        DB::commit();
                        return $course;
                    }
                }
            } else {
                // Course code doesn't exist - create new course
                $course = Course::create([
                    'course_code' => $courseCode,
                    'course_title' => $courseTitle,
                    'department_id' => $departmentId,
                    'level' => $level,
                    'semester' => 'both',
                    'credit_units' => 3,
                    'status' => 'active',
                ]);
                DB::commit();
                return $course;
            }

        } catch (\Exception $e) {
            DB::rollBack();

            // If it's still a duplicate entry error, try a different approach
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                // Generate a unique course code by adding timestamp
                $uniqueCourseCode = $courseCode . '_' . time();

                try {
                    DB::beginTransaction();
                    $course = Course::create([
                        'course_code' => $uniqueCourseCode,
                        'course_title' => $courseTitle,
                        'department_id' => $departmentId,
                        'level' => $level,
                        'semester' => 'both',
                        'credit_units' => 3,
                        'status' => 'active',
                    ]);
                    DB::commit();
                    return $course;
                } catch (\Exception $e2) {
                    DB::rollBack();
                    throw new \Exception('Unable to create course: ' . $e2->getMessage());
                }
            }

            throw $e;
        }
    }

    /**
     * Get which days of the week fall within the target month.
     *
     * @param \Carbon\Carbon $weekStart
     * @param \Carbon\Carbon $weekEnd
     * @param int $month
     * @param int $year
     * @return array
     */
    private function getDaysInMonth($weekStart, $weekEnd, $month, $year)
    {
        $daysInMonth = [];
        $currentDate = $weekStart->copy();

        while ($currentDate->lte($weekEnd)) {
            $dayName = strtolower($currentDate->format('l'));
            $daysInMonth[$dayName] = [
                'date' => $currentDate->format('Y-m-d'),
                'day_number' => $currentDate->day,
                'is_in_target_month' => $currentDate->month == $month && $currentDate->year == $year,
                'is_today' => $currentDate->isToday(),
                'is_weekend' => $currentDate->isWeekend()
            ];

            $currentDate->addDay();
        }

        return $daysInMonth;
    }

    /**
     * Get current week schedule with enhanced filtering.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentWeekSchedule(Request $request)
    {
        $lecturer = Auth::user()->lecturerProfile;

        try {
            // Get current week dates
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            // Get all active timetables for the lecturer
            $timetables = Timetable::with(['course', 'faculty', 'department'])
                ->where('lecturer_id', $lecturer->id)
                ->active()
                ->get();

            // Update session dates for recurring timetables
            foreach ($timetables as $timetable) {
                if ($timetable->is_recurring && (!$timetable->session_dates || empty($timetable->session_dates))) {
                    $timetable->updateSessionDates();
                }
            }

            // Get current week schedule
            $weekSchedule = $this->getWeekSchedule($startOfWeek, $endOfWeek, $timetables);

            return response()->json([
                'success' => true,
                'week_start' => $startOfWeek->format('Y-m-d'),
                'week_end' => $endOfWeek->format('Y-m-d'),
                'week_label' => $startOfWeek->format('M d') . ' - ' . $endOfWeek->format('M d, Y'),
                'schedule' => $weekSchedule
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load current week schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get session details for a specific date.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSessionDetails(Request $request)
    {
        $lecturer = Auth::user()->lecturerProfile;
        $date = $request->get('date');

        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        try {
            $targetDate = Carbon::parse($date);
            $dayOfWeek = strtolower($targetDate->format('l'));

            // Get timetables for this day
            $timetables = Timetable::with(['course', 'faculty', 'department'])
                ->where('lecturer_id', $lecturer->id)
                ->where('day_of_week', $dayOfWeek)
                ->active()
                ->get();

            $sessions = [];

            foreach ($timetables as $timetable) {
                // Check if this timetable is active for the target date
                if (method_exists($timetable, 'isActiveForDate') && $timetable->isActiveForDate($date)) {
                    $sessions[] = [
                        'id' => $timetable->id,
                        'course_code' => $timetable->course->course_code ?? 'N/A',
                        'course_title' => $timetable->course->course_title ?? 'N/A',
                        'start_time' => is_object($timetable->start_time) ? $timetable->start_time->format('H:i') : $timetable->start_time,
                        'end_time' => is_object($timetable->end_time) ? $timetable->end_time->format('H:i') : $timetable->end_time,
                        'venue' => $timetable->venue,
                        'level' => $timetable->level,
                        'is_completed' => in_array($date, $timetable->completed_dates ?? []),
                        'notes' => $timetable->notes
                    ];
                }
            }

            // Sort sessions by start time
            usort($sessions, function($a, $b) {
                return strcmp($a['start_time'], $b['start_time']);
            });

            return response()->json([
                'success' => true,
                'date' => $date,
                'day_name' => $targetDate->format('l'),
                'formatted_date' => $targetDate->format('M d, Y'),
                'sessions' => $sessions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load session details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function debugWeeklySchedule(Request $request)
{
    $lecturer = Auth::user()->lecturerProfile;
    
    // Get current week date range
    $startOfWeek = Carbon::now()->startOfWeek(); // Monday
    $endOfWeek = Carbon::now()->endOfWeek(); // Sunday
    
    // Get all active timetables for the lecturer
    $timetables = Timetable::with(['course', 'faculty', 'department'])
        ->where('lecturer_id', $lecturer->id)
        ->active()
        ->get();
    
    $debugData = [];
    
    foreach ($timetables as $timetable) {
        $hasSession = $this->hasSessionThisWeek($timetable, $startOfWeek, $endOfWeek);
        
        $debugData[] = [
            'id' => $timetable->id,
            'course_code' => $timetable->course->course_code ?? 'N/A',
            'day_of_week' => $timetable->day_of_week,
            'start_time' => $timetable->start_time,
            'end_time' => $timetable->end_time,
            'effective_date' => $timetable->effective_date,
            'end_date' => $timetable->end_date,
            'is_recurring' => $timetable->is_recurring,
            'status' => $timetable->status,
            'has_session_this_week' => $hasSession,
            'session_dates' => $timetable->session_dates,
            'completed_dates' => $timetable->completed_dates,
        ];
    }
    
    return response()->json([
        'start_of_week' => $startOfWeek->format('Y-m-d'),
        'end_of_week' => $endOfWeek->format('Y-m-d'),
        'timetables_count' => count($timetables),
        'timetables' => $debugData
    ]);
}
}
