<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use App\Models\User;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{

    public function dashboard()
    {
        // Get statistics
        $totalStudents = Student::count();
        $totalLecturers = Lecturer::count();
        $totalFaculties = Faculty::count();
        $totalDepartments = Department::count();
        
        // Get new registrations this month
        $currentMonth = now()->startOfMonth();
        $newStudentsThisMonth = Student::where('created_at', '>=', $currentMonth)->count();
        $newLecturersThisMonth = Lecturer::where('created_at', '>=', $currentMonth)->count();
        
        // Get recent activities (last 10 activities)
        $recentActivities = collect();
        
        // Recent students
        $recentStudents = Student::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($student) {
                return [
                    'type' => 'student',
                    'icon' => 'fa-user-graduate',
                    'message' => "New student {$student->user->name} was registered",
                    'time' => $student->created_at->diffForHumans()
                ];
            });
        
        // Recent lecturers
        $recentLecturers = Lecturer::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($lecturer) {
                return [
                    'type' => 'lecturer',
                    'icon' => 'fa-chalkboard-teacher',
                    'message' => "New lecturer {$lecturer->user->name} was added",
                    'time' => $lecturer->created_at->diffForHumans()
                ];
            });
        
        // Merge and sort activities
        $recentActivities = $recentStudents->concat($recentLecturers)
            ->sortByDesc(function ($activity) {
                return $activity['time'];
            })
            ->take(10)
            ->values();
        
        return view('admin.dashboard', [
            'title' => 'Dashboard - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'totalStudents' => $totalStudents,
            'totalLecturers' => $totalLecturers,
            'totalFaculties' => $totalFaculties,
            'totalDepartments' => $totalDepartments,
            'newStudentsThisMonth' => $newStudentsThisMonth,
            'newLecturersThisMonth' => $newLecturersThisMonth,
            'recentActivities' => $recentActivities
        ]);
    }
    
    public function profile()
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->isAdmin()) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }
        
        // Get or create admin profile
        $adminProfile = $user->adminProfile ?? $user->adminProfile()->create();
        
        return view('admin.profile', [
            'title' => 'Profile - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'user' => $user,
            'adminProfile' => $adminProfile
        ]);
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->isAdmin()) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }
        
        // Get or create admin profile
        $adminProfile = $user->adminProfile ?? $user->adminProfile()->create();
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Validate the image
            $request->validate([
                'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            
            // Store new image with a unique name to avoid caching issues
            $imageName = time() . '_' . uniqid() . '.' . $request->profile_image->extension();
            
            // Delete old image if exists
            if ($adminProfile->profile_image) {
                Storage::disk('public')->delete('admin_images/' . $adminProfile->profile_image);
            }
            
            // Store the new image
            $request->file('profile_image')->storeAs('admin_images', $imageName, 'public');
            
            // Update admin profile with new image
            $adminProfile->update([
                'profile_image' => $imageName,
            ]);
            
            // If it's an AJAX request, return JSON response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile image updated successfully',
                    'image_url' => asset('storage/admin_images/' . $imageName) . '?v=' . time()
                ]);
            }
            
            return redirect()->route('admin.profile')->with('success', 'Profile image updated successfully');
        }
        
        // Handle other profile updates
        if ($request->has('name') || $request->has('phone') || $request->has('address')) {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);
            
            // Update user name
            if ($request->has('name')) {
                $user->update([
                    'name' => $validated['name'],
                ]);
            }
            
            // Update admin profile
            $adminProfile->update([
                'phone' => $validated['phone'] ?? $adminProfile->phone,
                'address' => $validated['address'] ?? $adminProfile->address,
            ]);
            
            // If it's an AJAX request, return JSON response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            }
            
            return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
        }
        
        return redirect()->route('admin.profile');
    }
    
    /**
     * Display a listing of lecturers with search functionality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function lecturer(Request $request)
    {
        $search = $request->input('search');
        
        $lecturersQuery = Lecturer::with(['user', 'department.faculty']);
        
        // Apply search filter if search term is provided
        if ($search) {
            $lecturersQuery->whereHas('user', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('staff_id', 'like', "%{$search}%")
            ->orWhere('phone_number', 'like', "%{$search}%")
            ->orWhereHas('department', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }
        
        $lecturers = $lecturersQuery->get();
        
        return view('admin.lecturer', [
            'title' => 'Lecturer Management - Affan Student Timetable',
            'description' => 'Manage lecturers in the system',
            'ogImage' => url('images/icons/favicon.png'),
            'lecturers' => $lecturers,
            'search' => $search
        ]);
    }
    
    /**
     * Display a listing of students with search functionality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function student(Request $request)
    {
        $search = $request->input('search');
        
        $studentsQuery = Student::with(['user', 'department.faculty']);
        
        // Apply search filter if search term is provided
        if ($search) {
            $studentsQuery->whereHas('user', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('matric_number', 'like', "%{$search}%")
            ->orWhereHas('department', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('department.faculty', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }
        
        $students = $studentsQuery->get();
        
        return view('admin.student', [
            'title' => 'Student Management - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'students' => $students,
            'search' => $search
        ]);
    }
    
    /**
     * Show the form for creating a new student.
     */
    public function createStudent()
    {
        $faculties = Faculty::with('departments')->get();
        
        return view('admin.create-student', [
            'title' => 'Create Student - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'faculties' => $faculties
        ]);
    }
    
    /**
     * Generate a matric number for a new student.
     *
     * @param  string  $departmentId
     * @return string
     */
    private function generateMatricNumber($departmentId)
    {
        $department = Department::with('faculty')->findOrFail($departmentId);
        $facultyCode = $department->faculty->code;
        $departmentCode = $department->code;
        $currentYear = date('Y');
        
        // Get the highest sequential number for this department
        $highestMatricNumber = Student::where('matric_number', 'like', "{$currentYear}/{$facultyCode}/{$departmentCode}/%")
            ->orderByRaw('CAST(SUBSTRING_INDEX(matric_number, "/", -1) AS UNSIGNED) DESC')
            ->value('matric_number');
        
        if ($highestMatricNumber) {
            // Extract the sequential number and increment it
            $parts = explode('/', $highestMatricNumber);
            $sequentialNumber = (int)end($parts) + 1;
        } else {
            // Start with 1 if no existing students
            $sequentialNumber = 1;
        }
        
        // Format the sequential number with leading zeros
        $formattedNumber = str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);
        
        return "{$currentYear}/{$facultyCode}/{$departmentCode}/{$formattedNumber}";
    }
    
    /**
     * Store a newly created student in storage.
     */
    public function storeStudent(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'address' => 'nullable|string',
            'faculty' => 'required|string|exists:faculties,id',
            'department' => 'required|string|exists:departments,id',
            'level' => 'required|integer|in:100,200,300,400',
            'status' => 'required|string|in:active,inactive,suspended,graduated',
            'matricNumber' => 'nullable|string|unique:students,matric_number',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $validated['fullName'],
                'email' => $validated['email'],
                'password' => Hash::make('password'), // Default password
                'role' => 'student',
                'first_login' => true, // Force password change on first login
            ]);

            // Handle profile picture upload
            $profileImagePath = null;
            if ($request->hasFile('profilePicture')) {
                $profileImagePath = $request->file('profilePicture')->store('student_images', 'public');
            }

            // Generate matric number if not provided
            $matricNumber = $validated['matricNumber'] ?? $this->generateMatricNumber($validated['department']);

            // Create student profile
            $student = Student::create([
                'user_id' => $user->id,
                'department_id' => $validated['department'],
                'matric_number' => $matricNumber,
                'level' => $validated['level'],
                'status' => $validated['status'],
                'address' => $validated['address'],
                'profile_image' => $profileImagePath,
            ]);

            DB::commit();

            return redirect()->route('admin.student')->with('success', 'Student created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if (isset($profileImagePath) && Storage::disk('public')->exists($profileImagePath)) {
                Storage::disk('public')->delete($profileImagePath);
            }

            return back()->withInput()->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified student.
     */
    public function editStudent($id)
    {
        $student = Student::with(['user', 'department.faculty'])->findOrFail($id);
        $faculties = Faculty::with('departments')->get();
        
        return view('admin.edit-student', [
            'title' => 'Edit Student - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            'student' => $student,
            'faculties' => $faculties
        ]);
    }
    
    /**
     * Update the specified student in storage.
     */
    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'address' => 'nullable|string',
            'faculty' => 'required|string|exists:faculties,id',
            'department' => 'required|string|exists:departments,id',
            'level' => 'required|integer|in:100,200,300,400',
            'status' => 'required|string|in:active,inactive,suspended,graduated',
            'matricNumber' => [
                'required',
                'string',
                Rule::unique('students', 'matric_number')->ignore($student->id),
            ],
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Update user
            $student->user->update([
                'name' => $validated['fullName'],
                'email' => $validated['email'],
            ]);

            // Handle profile picture upload
            if ($request->hasFile('profilePicture')) {
                // Delete old image if exists
                if ($student->profile_image && Storage::disk('public')->exists($student->profile_image)) {
                    Storage::disk('public')->delete($student->profile_image);
                }
                
                $profileImagePath = $request->file('profilePicture')->store('student_images', 'public');
                $student->profile_image = $profileImagePath;
            }

            // Update student profile
            $student->update([
                'department_id' => $validated['department'],
                'matric_number' => $validated['matricNumber'],
                'level' => $validated['level'],
                'status' => $validated['status'],
                'address' => $validated['address'],
            ]);

            DB::commit();

            return redirect()->route('admin.student')->with('success', 'Student updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified student from storage.
     */
    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        
        // Begin transaction
        DB::beginTransaction();

        try {
            // Delete profile image if exists
            if ($student->profile_image && Storage::disk('public')->exists($student->profile_image)) {
                Storage::disk('public')->delete($student->profile_image);
            }
            
            // Delete user (will cascade delete student due to foreign key constraint)
            $student->user->delete();

            DB::commit();

            return redirect()->route('admin.student')->with('success', 'Student deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for creating a new lecturer.
     *
     * @return \Illuminate\View\View
     */
    public function createLecturer()
    {
        $faculties = Faculty::with('departments')->get();
        
        return view('admin.create-lecturer', [
            'title' => 'Create Lecturer - Affan Student Timetable',
            'description' => 'Add a new lecturer to the system',
            'ogImage' => url('images/icons/favicon.png'),
            'faculties' => $faculties
        ]);
    }

    /**
     * Store a newly created lecturer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeLecturer(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'faculty' => 'required|string|exists:faculties,id',
            'department' => 'required|string|exists:departments,id',
            'staff_id' => 'nullable|string|unique:lecturers,staff_id',
            'status' => 'required|string|in:active,inactive,on_leave,retired',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $validated['fullName'],
                'email' => $validated['email'],
                'password' => Hash::make('password'), // Default password
                'role' => 'lecturer',
                'first_login' => true, // Force password change on first login
            ]);

            // Handle profile picture upload
            $profileImagePath = null;
            if ($request->hasFile('profilePicture')) {
                $profileImagePath = $request->file('profilePicture')->store('lecturer_images', 'public');
            }

            // Generate staff ID if not provided
            $staffId = $validated['staff_id'] ?? $this->generateStaffId($validated['department']);

            // Create lecturer profile
            $lecturer = Lecturer::create([
                'user_id' => $user->id,
                'department_id' => $validated['department'],
                'staff_id' => $staffId,
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'profile_image' => $profileImagePath,
                'status' => $validated['status'],
            ]);

            DB::commit();

            return redirect()->route('admin.lecturer')->with('success', 'Lecturer created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if (isset($profileImagePath) && Storage::disk('public')->exists($profileImagePath)) {
                Storage::disk('public')->delete($profileImagePath);
            }

            return back()->withInput()->with('error', 'Failed to create lecturer: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified lecturer.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function viewLecturer($id)
    {
        $lecturer = Lecturer::with(['user', 'department.faculty'])->findOrFail($id);
        
        return view('admin.view-lecturer', [
            'title' => 'Lecturer Details - Affan Student Timetable',
            'description' => 'View lecturer details',
            'ogImage' => url('images/icons/favicon.png'),
            'lecturer' => $lecturer
        ]);
    }

    /**
     * Show the form for editing the specified lecturer.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editLecturer($id)
    {
        $lecturer = Lecturer::with(['user', 'department.faculty'])->findOrFail($id);
        $faculties = Faculty::with('departments')->get();
        
        return view('admin.edit-lecturer', [
            'title' => 'Edit Lecturer - Affan Student Timetable',
            'description' => 'Edit lecturer details',
            'ogImage' => url('images/icons/favicon.png'),
            'lecturer' => $lecturer,
            'faculties' => $faculties
        ]);
    }

    /**
     * Update the specified lecturer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLecturer(Request $request, $id)
    {
        $lecturer = Lecturer::findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($lecturer->user_id),
            ],
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'faculty' => 'required|string|exists:faculties,id',
            'department' => 'required|string|exists:departments,id',
            'staff_id' => [
                'required',
                'string',
                Rule::unique('lecturers', 'staff_id')->ignore($lecturer->id),
            ],
            'status' => 'required|string|in:active,inactive,on_leave,retired',
            'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Update user
            $lecturer->user->update([
                'name' => $validated['fullName'],
                'email' => $validated['email'],
            ]);

            // Handle profile picture upload
            if ($request->hasFile('profilePicture')) {
                // Delete old image if exists
                if ($lecturer->profile_image && Storage::disk('public')->exists($lecturer->profile_image)) {
                    Storage::disk('public')->delete($lecturer->profile_image);
                }
                
                $profileImagePath = $request->file('profilePicture')->store('lecturer_images', 'public');
                $lecturer->profile_image = $profileImagePath;
            }

            // Update lecturer profile
            $lecturer->update([
                'department_id' => $validated['department'],
                'staff_id' => $validated['staff_id'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'status' => $validated['status'],
            ]);

            DB::commit();

            return redirect()->route('admin.lecturer')->with('success', 'Lecturer updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'Failed to update lecturer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified lecturer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLecturer($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        
        // Begin transaction
        DB::beginTransaction();

        try {
            // Delete profile image if exists
            if ($lecturer->profile_image && Storage::disk('public')->exists($lecturer->profile_image)) {
                Storage::disk('public')->delete($lecturer->profile_image);
            }
            
            // Delete user (will cascade delete lecturer due to foreign key constraint)
            $lecturer->user->delete();

            DB::commit();

            return redirect()->route('admin.lecturer')->with('success', 'Lecturer deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to delete lecturer: ' . $e->getMessage());
        }
    }
    
    /**
     * Get departments for a faculty (AJAX request).
     */
    public function getDepartments(Request $request)
    {
        $facultyId = $request->input('faculty_id');
        $departments = Department::where('faculty_id', $facultyId)->get();
        
        return response()->json($departments);
    }
    
    /**
     * Generate a staff ID for a new lecturer.
     *
     * @param  string  $departmentId
     * @return string
     */
    private function generateStaffId($departmentId)
    {
        $department = Department::with('faculty')->findOrFail($departmentId);
        $facultyCode = $department->faculty->code;
        $departmentCode = $department->code;
        $currentYear = date('Y');
        
        // Get the highest sequential number for this department
        $highestStaffId = Lecturer::where('staff_id', 'like', "{$currentYear}/STAFF/{$departmentCode}/%")
            ->orderByRaw('CAST(SUBSTRING_INDEX(staff_id, "/", -1) AS UNSIGNED) DESC')
            ->value('staff_id');
        
        if ($highestStaffId) {
            // Extract the sequential number and increment it
            $parts = explode('/', $highestStaffId);
            $sequentialNumber = (int)end($parts) + 1;
        } else {
            // Start with 1 if no existing lecturers
            $sequentialNumber = 1;
        }
        
        // Format the sequential number with leading zeros
        $formattedNumber = str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);
        
        return "{$currentYear}/STAFF/{$departmentCode}/{$formattedNumber}";
    }

    /**
     * Display the specified student.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function viewStudent($id)
    {
        $student = Student::with(['user', 'department.faculty'])->findOrFail($id);
        
        return view('admin.view-student', [
            'title' => 'Student Details - Affan Student Timetable',
            'description' => 'View student details',
            'ogImage' => url('images/icons/favicon.png'),
            'student' => $student
        ]);
    }

}
