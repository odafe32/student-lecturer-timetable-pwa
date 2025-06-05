<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\PushNotificationController;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Message;
use App\Models\Student;
use App\Models\Timetable;
use Carbon\Carbon;

class LecturerController extends Controller
{
    /**
     * Show the lecturer dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        return view('lecturer.dashboard', [
            'title' => 'Dashboard Management - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
        ]);
    }

    public function dashboardStats()
    {
        try {
            $lecturer = Auth::user()->lecturerProfile;
            
            if (!$lecturer) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lecturer profile not found',
                    'stats' => [
                        'total' => 0,
                        'current_week_active' => 0,
                        'completed' => 0,
                        'total_sessions_completed' => 0,
                        'total_sessions_planned' => 0,
                        'overall_completion_percentage' => 0,
                        'total_messages' => 0
                    ]
                ]);
            }
            
            // Get timetable statistics
            $stats = [];
            
            // Check if timetables table exists
            if (Schema::hasTable('timetables')) {
                // Total timetables
                $stats['total'] = Timetable::where('lecturer_id', $lecturer->id)->count();
                
                // Current week active classes
                $stats['current_week_active'] = Timetable::where('lecturer_id', $lecturer->id)
                    ->where('status', 'active')
                    ->count(); // Simplified for testing
                
                // Completed timetables
                $stats['completed'] = Timetable::where('lecturer_id', $lecturer->id)
                    ->where('completion_status', 'completed')
                    ->count();
                
                // Total sessions completed
                $stats['total_sessions_completed'] = Timetable::where('lecturer_id', $lecturer->id)
                    ->sum('completed_sessions');
                
                // Total sessions planned
                $stats['total_sessions_planned'] = Timetable::where('lecturer_id', $lecturer->id)
                    ->sum('total_sessions');
                
                // Overall completion percentage
                $stats['overall_completion_percentage'] = $stats['total_sessions_planned'] > 0 
                    ? round(($stats['total_sessions_completed'] / $stats['total_sessions_planned']) * 100, 2)
                    : 0;
            } else {
                $stats['total'] = 0;
                $stats['current_week_active'] = 0;
                $stats['completed'] = 0;
                $stats['total_sessions_completed'] = 0;
                $stats['total_sessions_planned'] = 0;
                $stats['overall_completion_percentage'] = 0;
            }
            
            // Get message statistics
            if (Schema::hasTable('messages')) {
                $stats['total_messages'] = Message::where('sender_id', Auth::id())->count();
            } else {
                $stats['total_messages'] = 0;
            }
            
            // Log the stats for debugging
            \Log::info('Dashboard stats', ['stats' => $stats, 'lecturer_id' => $lecturer->id]);
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting dashboard stats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load statistics: ' . $e->getMessage(),
                'stats' => [
                    'total' => 0,
                    'current_week_active' => 0,
                    'completed' => 0,
                    'total_sessions_completed' => 0,
                    'total_sessions_planned' => 0,
                    'overall_completion_percentage' => 0,
                    'total_messages' => 0
                ]
            ]);
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
        try {
            $lecturer = Auth::user()->lecturerProfile;
            $date = $request->get('date');
            
            if (!$lecturer) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lecturer profile not found',
                    'sessions' => []
                ]);
            }
            
            if (!$date) {
                return response()->json([
                    'success' => false,
                    'error' => 'Date is required',
                    'sessions' => []
                ], 400);
            }
            
            $targetDate = Carbon::parse($date);
            $dayOfWeek = strtolower($targetDate->format('l'));
            
            \Log::info('Getting sessions for date', [
                'date' => $date,
                'day_of_week' => $dayOfWeek,
                'lecturer_id' => $lecturer->id
            ]);
            
            // Get timetables for this day
            $timetables = Timetable::with(['course', 'faculty', 'department'])
                ->where('lecturer_id', $lecturer->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('status', 'active')
                ->get();
            
            \Log::info('Found timetables for day', ['count' => $timetables->count()]);
            
            $sessions = [];
            
            foreach ($timetables as $timetable) {
                // Simplified check for active date - you may need to adjust this
                $isActive = true;
                
                if ($timetable->effective_date && Carbon::parse($timetable->effective_date)->gt($targetDate)) {
                    $isActive = false; // Not active yet
                }
                
                if ($timetable->end_date && Carbon::parse($timetable->end_date)->lt($targetDate)) {
                    $isActive = false; // Already ended
                }
                
                // Check if completed
                $isCompleted = false;
                if (property_exists($timetable, 'completed_dates') || isset($timetable->completed_dates)) {
                    $completedDates = $timetable->completed_dates;
                    if (is_array($completedDates)) {
                        $isCompleted = in_array($date, $completedDates);
                    }
                }
                
                if ($isActive) {
                    $sessions[] = [
                        'id' => $timetable->id,
                        'course_code' => $timetable->course ? $timetable->course->course_code : 'N/A',
                        'course_title' => $timetable->course ? $timetable->course->course_title : 'N/A',
                        'start_time' => is_object($timetable->start_time) ? $timetable->start_time->format('H:i') : $timetable->start_time,
                        'end_time' => is_object($timetable->end_time) ? $timetable->end_time->format('H:i') : $timetable->end_time,
                        'venue' => $timetable->venue ?? 'No venue',
                        'level' => $timetable->level ?? 'N/A',
                        'is_completed' => $isCompleted,
                        'notes' => $timetable->notes ?? ''
                    ];
                }
            }
            
            // Sort sessions by start time
            usort($sessions, function($a, $b) {
                return strcmp($a['start_time'], $b['start_time']);
            });
            
            \Log::info('Sessions for today', ['count' => count($sessions)]);
            
            return response()->json([
                'success' => true,
                'date' => $date,
                'day_name' => $targetDate->format('l'),
                'formatted_date' => $targetDate->format('M d, Y'),
                'sessions' => $sessions
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting session details', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load session details: ' . $e->getMessage(),
                'sessions' => []
            ], 500);
        }
    }

    /**
     * Get recent messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentMessages()
    {
        try {
            // Check if messages table exists
            if (!Schema::hasTable('messages')) {
                return response()->json([
                    'success' => true,
                    'messages' => []
                ]);
            }
            
            $messages = Message::where('sender_id', Auth::id())
                ->with(['faculty', 'department'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // If no messages found, return empty array
            if ($messages->isEmpty()) {
                \Log::info('No recent messages found for user', ['user_id' => Auth::id()]);
                return response()->json([
                    'success' => true,
                    'messages' => []
                ]);
            }
            
            $formattedMessages = $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'title' => $message->title,
                    'content' => $message->content,
                    'faculty_name' => $message->faculty ? $message->faculty->name : 'Unknown Faculty',
                    'department_name' => $message->department ? $message->department->name : 'Unknown Department',
                    'level' => $message->level,
                    'created_at' => $message->created_at,
                    'recipient_count' => method_exists($message, 'recipients') ? $message->recipients()->count() : 0
                ];
            });
            
            \Log::info('Recent messages retrieved', ['count' => $formattedMessages->count()]);
            
            return response()->json([
                'success' => true,
                'messages' => $formattedMessages
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting recent messages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load messages: ' . $e->getMessage(),
                'messages' => []
            ]);
        }
    }

    /**
     * Get upcoming classes for the next 7 days.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpcomingClasses()
    {
        try {
            $lecturer = Auth::user()->lecturerProfile;
            
            if (!$lecturer) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lecturer profile not found',
                    'upcoming' => []
                ]);
            }
            
            // Get today's date
            $today = Carbon::today();
            
            // Get the next 7 days
            $nextWeek = $today->copy()->addDays(7);
            
            // Get all active timetables for the lecturer
            $timetables = Timetable::with(['course'])
                ->where('lecturer_id', $lecturer->id)
                ->where('status', 'active')
                ->get();
            
            \Log::info('Fetched timetables for upcoming classes', [
                'lecturer_id' => $lecturer->id,
                'count' => $timetables->count()
            ]);
            
            if ($timetables->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'upcoming' => []
                ]);
            }
            
            $upcomingSessions = [];
            
            // Loop through each day in the next 7 days
            $currentDate = $today->copy()->addDay(); // Start from tomorrow
            while ($currentDate->lte($nextWeek)) {
                $dayOfWeek = strtolower($currentDate->format('l'));
                $dateString = $currentDate->format('Y-m-d');
                
                // Find timetables for this day
                foreach ($timetables as $timetable) {
                    if ($timetable->day_of_week === $dayOfWeek) {
                        // Simplified check for active date - you may need to adjust this
                        $isActive = true;
                        
                        if ($timetable->effective_date && Carbon::parse($timetable->effective_date)->gt($currentDate)) {
                            $isActive = false; // Not active yet
                        }
                        
                        if ($timetable->end_date && Carbon::parse($timetable->end_date)->lt($currentDate)) {
                            $isActive = false; // Already ended
                        }
                        
                        // Check if completed
                        $isCompleted = false;
                        if (property_exists($timetable, 'completed_dates') || isset($timetable->completed_dates)) {
                            $completedDates = $timetable->completed_dates;
                            if (is_array($completedDates)) {
                                $isCompleted = in_array($dateString, $completedDates);
                            }
                        }
                        
                        if ($isActive && !$isCompleted) {
                            $upcomingSessions[] = [
                                'id' => $timetable->id,
                                'date' => $dateString,
                                'day_name' => $currentDate->format('l'),
                                'course_code' => $timetable->course ? $timetable->course->course_code : 'N/A',
                                'course_title' => $timetable->course ? $timetable->course->course_title : 'N/A',
                                'start_time' => is_object($timetable->start_time) ? $timetable->start_time->format('H:i') : $timetable->start_time,
                                'end_time' => is_object($timetable->end_time) ? $timetable->end_time->format('H:i') : $timetable->end_time,
                                'venue' => $timetable->venue ?? 'No venue',
                                'level' => $timetable->level ?? 'N/A'
                            ];
                        }
                    }
                }
                
                $currentDate->addDay();
            }
            
            // Sort by date and time
            usort($upcomingSessions, function($a, $b) {
                if ($a['date'] === $b['date']) {
                    return strcmp($a['start_time'], $b['start_time']);
                }
                return strcmp($a['date'], $b['date']);
            });
            
            // Limit to 5 sessions
            $upcomingSessions = array_slice($upcomingSessions, 0, 5);
            
            \Log::info('Upcoming sessions', ['count' => count($upcomingSessions)]);
            
            return response()->json([
                'success' => true,
                'upcoming' => $upcomingSessions
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting upcoming classes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load upcoming classes: ' . $e->getMessage(),
                'upcoming' => []
            ]);
        }
    }

    public function timeTable()
    {
        return view('lecturer.time-table', [
            'title' => 'Time Table - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
        ]);
    }

    /**
     * Show the messages page with form to send new messages and list of sent messages.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function messages(Request $request)
    {
        $faculties = Faculty::where('status', 'active')->get();
        $sentMessages = Message::where('sender_id', Auth::id())
            ->with(['faculty', 'department'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Initialize variables
        $departments = collect();
        $levels = collect();
        $selectedFaculty = null;
        $selectedDepartment = null;
        
        // If faculty_id is provided, load departments
        if ($request->has('faculty_id') && $request->faculty_id) {
            $selectedFaculty = Faculty::find($request->faculty_id);
            
            if ($selectedFaculty) {
                $departments = Department::where('faculty_id', $request->faculty_id)
                    ->where('status', 'active')
                    ->get();
            }
            
            // If department_id is also provided, load levels
            if ($request->has('department_id') && $request->department_id) {
                $selectedDepartment = Department::find($request->department_id);
                
                if ($selectedDepartment) {
                    // Get all available levels for this department
                    // This should be a fixed list of levels rather than from the database
                    $levels = [100, 200, 300, 400, 500];
                    
                    // Alternatively, if you want to get only levels that have students:
                    // $levels = Student::where('department_id', $request->department_id)
                    //     ->where('status', 'active')
                    //     ->distinct()
                    //     ->pluck('level')
                    //     ->sort()
                    //     ->values();
                }
            }
        }

        return view('lecturer.messages', [
            'title' => 'Messages - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'faculties' => $faculties,
            'departments' => $departments,
            'levels' => $levels,
            'selectedFaculty' => $selectedFaculty,
            'selectedDepartment' => $selectedDepartment,
            'sentMessages' => $sentMessages,
        ]);
    }

    /**
     * Get departments for a faculty (AJAX endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepartmentsByFacultyForMessages(Request $request)
    {
        $facultyId = $request->input('faculty_id');
        $departments = Department::where('faculty_id', $facultyId)
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json($departments);
    }

    /**
     * Get available student levels for a department (AJAX endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLevelsByDepartment(Request $request)
    {
        $departmentId = $request->input('department_id');
        
        // Get distinct levels from students in this department
        $levels = Student::where('department_id', $departmentId)
            ->where('status', 'active')
            ->distinct()
            ->pluck('level')
            ->sort()
            ->values();

        return response()->json($levels);
    }

    /**
     * Send a new message to students.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:100|max:900',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'faculty_id' => $request->faculty_id,
            'department_id' => $request->department_id,
            'level' => $request->level,
            'title' => $request->title,
            'content' => $request->content,
            'status' => 'active',
        ]);

        // Get count of recipients for feedback message
        $recipientCount = $message->recipients()->count();

        // If push notification controller exists, send notifications to students
        if (class_exists(PushNotificationController::class)) {
            $recipients = $message->recipients()->with('user')->get();
            foreach ($recipients as $student) {
                if ($student->user) {
                    app(PushNotificationController::class)->sendNotificationToUser(
                        $student->user,
                        $request->title,
                        $request->content
                    );
                }
            }
        }

        return redirect()->route('lecturer.messages')
            ->with('success', "Message sent successfully to {$recipientCount} students!");
    }

    /**
     * View message details.
     *
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function viewMessage($id)
    {
        $message = Message::where('sender_id', Auth::id())
            ->with(['faculty', 'department'])
            ->findOrFail($id);

        $recipients = $message->recipients()->with('user')->paginate(20);

        return view('lecturer.view-message', [
            'title' => 'View Message - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'message' => $message,
            'recipients' => $recipients,
        ]);
    }

    /**
     * Delete a message.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteMessage($id)
    {
        $message = Message::where('sender_id', Auth::id())->findOrFail($id);
        $message->delete();

        return redirect()->route('lecturer.messages')
            ->with('success', 'Message deleted successfully!');
    }

    /**
     * Show the lecturer profile page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        $lecturer = $user->lecturerProfile;
        return view('lecturer.profile', [
            'title' => 'Profile - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'user' => $user,
            'lecturer' => $lecturer,
        ]);
    }

    /**
     * Update the lecturer profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $lecturer = $user->lecturerProfile;

        // Update user table
        $user->update([
            'name' => $request->name,
        ]);

        // Update or create lecturer profile
        if ($lecturer) {
            $lecturer->update([
                'phone_number' => $request->phone,
                'address' => $request->address,
            ]);
        } else {
            // Create lecturer profile if it doesn't exist
            $user->lecturerProfile()->create([
                'phone_number' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
            ]);
        }

        return redirect()->route('lecturer.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the lecturer profile image.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        $lecturer = $user->lecturerProfile;

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($lecturer && $lecturer->profile_image) {
                Storage::disk('public')->delete($lecturer->profile_image);
            }

            // Store new image
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');

            // Update or create lecturer profile
            if ($lecturer) {
                $lecturer->update([
                    'profile_image' => $imagePath,
                ]);
            } else {
                $user->lecturerProfile()->create([
                    'profile_image' => $imagePath,
                    'status' => 'active',
                ]);
            }
        }

        return redirect()->route('lecturer.profile')->with('success', 'Profile image updated successfully!');
    }

    /**
     * Update the lecturer password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('lecturer.profile')
                ->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('lecturer.profile')->with('success', 'Password updated successfully!');
    }
}