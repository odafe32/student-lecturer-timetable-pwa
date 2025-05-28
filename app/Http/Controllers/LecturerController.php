<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\PushNotificationController;

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
    public function timeTable()
    {
     
        return view('lecturer.time-table', [
            'title' => 'Time Table  - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
        ]);
    }
    public function messages()
    {
     
        return view('lecturer.messages', [
            'title' => 'Messages  - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('images/icons/favicon.png'),
        ]);
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
            'title' => 'Profile  - Affan Student Timetable',
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
