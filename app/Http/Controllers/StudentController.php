<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use App\Models\Student;
use App\Models\User;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\Timetable;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        return view('student.dashboard', [
            'title' => 'Dashboard Management - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'user' => $user,
            'student' => $student,
        ]);
    }

    public function messages(Request $request)
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        // Build the base query
        $query = Message::query()
            ->where(function($query) use ($student) {
                // Messages targeted to this student's department and level
                $query->where('department_id', $student->department_id)
                      ->where('level', $student->level);
                
                // Or messages targeted to all students in this department (level = null)
                $query->orWhere(function($q) use ($student) {
                    $q->where('department_id', $student->department_id)
                      ->whereNull('level');
                });
                
                // Or messages targeted to all students in this faculty
                $query->orWhere(function($q) use ($student) {
                    $facultyId = $student->department->faculty_id;
                    $q->where('faculty_id', $facultyId)
                      ->whereNull('department_id');
                });
                
                // Or messages targeted to all students
                $query->orWhere(function($q) {
                    $q->whereNull('faculty_id')
                      ->whereNull('department_id')
                      ->whereNull('level');
                });
            })
            ->where('status', 'active');
        
        // Apply filter for unread messages if requested
        if ($request->query('filter') === 'unread') {
            $query->whereDoesntHave('readBy', function($q) use ($student) {
                $q->where('student_id', $student->id);
            });
        }
        
        // Get the messages ordered by creation date
        $messages = $query->orderBy('created_at', 'desc')->get();
        
        return view('student.messages', [
            'title' => 'Messages - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'user' => $user,
            'student' => $student,
            'messages' => $messages
        ]);
    }

    /**
     * Display a specific message and mark it as read
     *
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function viewMessage($id)
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        // Find the message
        $message = Message::findOrFail($id);
        
        // Check if this student should have access to this message
        $hasAccess = false;
        
        // Message targeted to this student's department and level
        if ($message->department_id == $student->department_id && $message->level == $student->level) {
            $hasAccess = true;
        }
        
        // Message targeted to all students in this department (level = null)
        elseif ($message->department_id == $student->department_id && $message->level === null) {
            $hasAccess = true;
        }
        
        // Message targeted to all students in this faculty
        elseif ($message->faculty_id == $student->department->faculty_id && $message->department_id === null) {
            $hasAccess = true;
        }
        
        // Message targeted to all students
        elseif ($message->faculty_id === null && $message->department_id === null && $message->level === null) {
            $hasAccess = true;
        }
        
        // If student doesn't have access, redirect back with error
        if (!$hasAccess) {
            return redirect()->route('student.messages')->with('error', 'You do not have permission to view this message.');
        }
        
        // Automatically mark the message as read when viewed
        if (!$student->hasReadMessage($message->id)) {
            $student->markMessageAsRead($message->id);
        }
        
        return view('student.view-message', [
            'title' => 'View Message - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'user' => $user,
            'student' => $student,
            'message' => $message
        ]);
    }

    /**
     * Toggle the read status of a message
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleMessageReadStatus($id)
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        // Find the message
        $message = Message::findOrFail($id);
        
        // Check if this student should have access to this message
        $hasAccess = false;
        
        // Message targeted to this student's department and level
        if ($message->department_id == $student->department_id && $message->level == $student->level) {
            $hasAccess = true;
        }
        
        // Message targeted to all students in this department (level = null)
        elseif ($message->department_id == $student->department_id && $message->level === null) {
            $hasAccess = true;
        }
        
        // Message targeted to all students in this faculty
        elseif ($message->faculty_id == $student->department->faculty_id && $message->department_id === null) {
            $hasAccess = true;
        }
        
        // Message targeted to all students
        elseif ($message->faculty_id === null && $message->department_id === null && $message->level === null) {
            $hasAccess = true;
        }
        
        // If student doesn't have access, return error
        if (!$hasAccess) {
            return redirect()->route('student.messages')->with('error', 'You do not have permission to access this message.');
        }
        
        // Toggle read status
        if ($student->hasReadMessage($message->id)) {
            // If already read, mark as unread by deleting the read record
            MessageRead::where('message_id', $message->id)
                ->where('student_id', $student->id)
                ->delete();
            $status = 'unread';
        } else {
            // If unread, mark as read
            $student->markMessageAsRead($message->id);
            $status = 'read';
        }
        
        // If it's an AJAX request, return JSON
        if (request()->ajax()) {
            return response()->json(['success' => true, 'status' => $status]);
        }
        
        // Otherwise redirect back with success message
        return redirect()->back()->with('success', "Message marked as $status.");
    }

    public function markMessageAsRead($id)
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        // Find the message
        $message = Message::findOrFail($id);
        
        // Check if this student should have access to this message
        $hasAccess = false;
        
        // Message targeted to this student's department and level
        if ($message->department_id == $student->department_id && $message->level == $student->level) {
            $hasAccess = true;
        }
        
        // Message targeted to all students in this department (level = null)
        elseif ($message->department_id == $student->department_id && $message->level === null) {
            $hasAccess = true;
        }
        
        // Message targeted to all students in this faculty
        elseif ($message->faculty_id == $student->department->faculty_id && $message->department_id === null) {
            $hasAccess = true;
        }
        
        // Message targeted to all students
        elseif ($message->faculty_id === null && $message->department_id === null && $message->level === null) {
            $hasAccess = true;
        }
        
        // If student doesn't have access, return error
        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to access this message.'], 403);
        }
        
        // Mark the message as read
        $student->markMessageAsRead($id);
        
        return response()->json(['success' => true]);
    }

    /**
     * Display the student's timetable based on their faculty, department, and level
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function timetable(Request $request)
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        // Get filter parameters
        $filter = $request->get('filter', 'current');
        $day = $request->get('day', strtolower(Carbon::now()->format('l')));
        
        // Build the base query to get timetables for this student's faculty, department, and level
        $query = Timetable::with(['course', 'faculty', 'department', 'lecturer'])
            ->where('status', 'active')
            ->where('faculty_id', $student->department->faculty_id)
            ->where('department_id', $student->department_id)
            ->where('level', $student->level);
        
        // Apply filters
        switch ($filter) {
            case 'current':
                $query->currentWeek();
                break;
            case 'today':
                $query->where('day_of_week', strtolower(Carbon::now()->format('l')));
                break;
            case 'all':
                // No additional filters
                break;
            case 'day':
                if (in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
                    $query->where('day_of_week', $day);
                }
                break;
        }
        
        // Get timetables ordered by day and time
        $timetables = $query->orderBy('day_of_week')
                           ->orderBy('start_time')
                           ->get();
        
        // Group timetables by day for easier display
        $weeklySchedule = $this->groupTimetablesByDay($timetables);
        
        // Get today's timetables for quick access
        $today = strtolower(Carbon::now()->format('l'));
        $todayTimetables = $timetables->where('day_of_week', $today)->values();
        
        return view('student.view-timetable', [
            'title' => 'Timetable - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'user' => $user,
            'student' => $student,
            'weeklySchedule' => $weeklySchedule,
            'todayTimetables' => $todayTimetables,
            'currentFilter' => $filter,
            'currentDay' => $day,
            'today' => $today
        ]);
    }
    
    /**
     * Get timetable for a specific day via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimetableByDay(Request $request)
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        $day = $request->get('day', strtolower(Carbon::now()->format('l')));
        
        if (!in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
            return response()->json(['error' => 'Invalid day selected'], 400);
        }
        
        $timetables = Timetable::with(['course', 'faculty', 'department', 'lecturer'])
            ->where('status', 'active')
            ->where('faculty_id', $student->department->faculty_id)
            ->where('department_id', $student->department_id)
            ->where('level', $student->level)
            ->where('day_of_week', $day)
            ->orderBy('start_time')
            ->get();
        
        return response()->json([
            'success' => true,
            'day' => $day,
            'timetables' => $timetables
        ]);
    }

    /**
     * Group timetables by day of week
     *
     * @param \Illuminate\Database\Eloquent\Collection $timetables
     * @return array
     */
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
     * Display the student profile page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        return view('student.profile', [
            'title' => 'Profile - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'user' => $user,
            'student' => $student,
        ]);
    }

    /**
     * Update the student profile information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        $student = $user->studentProfile;
        if ($student) {
            $student->phone_number = $request->phone;
            $student->address = $request->address;
            $student->save();
        }

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the student's password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('student.profile')->with('success', 'Password changed successfully!');
    }
    
    /**
     * Update the student's profile image.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfileImage(Request $request)
    {
        \Log::info('Profile image update started', ['user_id' => Auth::id()]);
        
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $user = Auth::user();
        $student = $user->studentProfile;
        
        \Log::info('Student profile found', ['student_id' => $student ? $student->id : 'null']);
    
        if ($request->hasFile('profile_image')) {
            \Log::info('Image file found in request');
            
            // Delete old image if exists
            if ($student && $student->profile_image) {
                \Log::info('Deleting old image', ['path' => $student->profile_image]);
                Storage::disk('public')->delete($student->profile_image);
            }
    
            // Store new image
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            \Log::info('New image stored', ['path' => $imagePath]);
    
            // Update or create student profile
            if ($student) {
                $student->update([
                    'profile_image' => $imagePath,
                ]);
                \Log::info('Student profile updated');
            } else {
                $user->studentProfile()->create([
                    'profile_image' => $imagePath,
                    'status' => 'active',
                ]);
                \Log::info('New student profile created');
            }
        } else {
            \Log::warning('No image file in request');
        }
    
        return redirect()->route('student.profile')->with('success', 'Profile image updated successfully!');
    }



    
    /**
     * Get timetable data for a specific month
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMonthData(Request $request)
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        
        try {
            // Build the query to get timetables for this student's faculty, department, and level
            $timetables = Timetable::with(['course', 'faculty', 'department', 'lecturer.user'])
                ->where('status', 'active')
                ->where('faculty_id', $student->department->faculty_id)
                ->where('department_id', $student->department_id)
                ->where('level', $student->level)
                ->get();
            
            // Filter timetables that are active during this month
            $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            
            $filteredTimetables = $timetables->filter(function($timetable) use ($startOfMonth, $endOfMonth) {
                // Check if timetable is active during this month
                $effectiveDate = $timetable->effective_date ? Carbon::parse($timetable->effective_date) : null;
                $endDate = $timetable->end_date ? Carbon::parse($timetable->end_date) : null;
                
                // If no effective date, assume it's always active
                if (!$effectiveDate) {
                    return true;
                }
                
                // If effective date is after end of month, it's not active this month
                if ($effectiveDate->gt($endOfMonth)) {
                    return false;
                }
                
                // If end date is before start of month, it's not active this month
                if ($endDate && $endDate->lt($startOfMonth)) {
                    return false;
                }
                
                return true;
            });
            
            return response()->json([
                'success' => true,
                'month' => $month,
                'year' => $year,
                'timetables' => $filteredTimetables->values()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load month data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get timetable data for a specific week
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeekData(Request $request)
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        $direction = $request->get('direction', 'current');
        $currentDate = $request->get('current_date');
        
        try {
            // Determine the week range based on direction
            $startOfWeek = null;
            $endOfWeek = null;
            
            if ($currentDate) {
                // Parse the current date from the format "Jan 01"
                $parsedDate = Carbon::parse($currentDate);
                
                if ($direction === 'prev') {
                    $startOfWeek = $parsedDate->copy()->subWeek()->startOfWeek();
                } elseif ($direction === 'next') {
                    $startOfWeek = $parsedDate->copy()->addWeek()->startOfWeek();
                } else {
                    $startOfWeek = $parsedDate->copy()->startOfWeek();
                }
            } else {
                // Use current week if no date provided
                $startOfWeek = Carbon::now()->startOfWeek();
            }
            
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            
            // Build the query to get timetables for this student's faculty, department, and level
            $timetables = Timetable::with(['course', 'faculty', 'department', 'lecturer.user'])
                ->where('status', 'active')
                ->where('faculty_id', $student->department->faculty_id)
                ->where('department_id', $student->department_id)
                ->where('level', $student->level)
                ->get();
            
            // Filter timetables that are active during this week
            $filteredTimetables = $timetables->filter(function($timetable) use ($startOfWeek, $endOfWeek) {
                return $timetable->hasSessionInWeek($startOfWeek, $endOfWeek);
            });
            
            // Group timetables by day
            $weeklySchedule = $this->groupTimetablesByDay($filteredTimetables);
            
            return response()->json([
                'success' => true,
                'week_range' => $startOfWeek->format('M d') . ' - ' . $endOfWeek->format('M d, Y'),
                'start_of_week' => $startOfWeek->format('Y-m-d'),
                'end_of_week' => $endOfWeek->format('Y-m-d'),
                'weekly_schedule' => $weeklySchedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load week data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export timetable to PDF
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        $user = Auth::user();
        $student = $user->studentProfile;
        
        // Get timetables for this student
        $timetables = Timetable::with(['course', 'faculty', 'department', 'lecturer.user'])
            ->where('status', 'active')
            ->where('faculty_id', $student->department->faculty_id)
            ->where('department_id', $student->department_id)
            ->where('level', $student->level)
            ->get();
        
        // Group timetables by day
        $weeklySchedule = $this->groupTimetablesByDay($timetables);
        
        // You would use a PDF library here like DomPDF or TCPDF
        // For now, just return a view that can be printed
        return view('student.timetable-pdf', [
            'title' => 'Timetable - ' . $student->user->name,
            'student' => $student,
            'weeklySchedule' => $weeklySchedule
        ]);
    }
}