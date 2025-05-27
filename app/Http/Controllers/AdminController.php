<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use App\Models\User;
use App\Models\Admin;
use App\Models\Student;
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
        return view('admin.dashboard', [
            'title' => 'Dashboard - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            
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
    
    public function lecturer()
    {
        return view('admin.lecturer', [
            'title' => 'Lecturer - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            
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
     * Get departments for a faculty (AJAX request).
     */
    public function getDepartments(Request $request)
    {
        $facultyId = $request->input('faculty_id');
        $departments = Department::where('faculty_id', $facultyId)->get();
        
        return response()->json($departments);
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
