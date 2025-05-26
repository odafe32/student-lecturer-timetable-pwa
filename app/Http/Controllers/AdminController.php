<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
    
    public function student()
    {
        return view('admin.student', [
            'title' => 'student - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            
        ]);
    }
    
    public function CreateStudent()
    {
        return view('admin.create-student', [
            'title' => 'Create Student - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            
        ]);
    }
    public function EditStudent()
    {
        return view('admin.edit-student', [
            'title' => 'Edit Student - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
            
        ]);
    }
    


}
