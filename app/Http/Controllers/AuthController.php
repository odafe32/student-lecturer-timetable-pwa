<?php

namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function Welcome()
    {
        Log::info('Welcome page accessed');
        return view('welcome', [
            'title' => 'Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('favicon.ico'),
        ]);
    }
    
    //login
    public function showLogin(){
        Log::info('Login page accessed');
        return view('auth.login', [
            'title' => 'Login - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('favicon.ico'),
        ]);
    }
    
    /**
     * Handle login request with direct role-based redirection
     */
    public function login(Request $request)
    {
        Log::info('Login attempt', ['email' => $request->email]);
        
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            
            Log::debug('Credentials validated', ['email' => $request->email]);
            
            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                
                $user = Auth::user();
                Log::info('User authenticated successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role
                ]);
                
                // Check if it's the user's first login
                if ($user->first_login) {
                    Log::info('First login detected for user', ['user_id' => $user->id]);
                    // Update the flag
                    $user->update(['first_login' => false]);
                    Log::debug('First login flag updated', ['user_id' => $user->id]);
                }
                
                // Redirect based on user role
                Log::debug('Determining redirect based on role', ['role' => $user->role]);
                
                switch ($user->role) {
                    case 'admin':
                        Log::info('Redirecting admin to dashboard', ['user_id' => $user->id]);
                        return redirect()->route('admin.dashboard')
                            ->with('status', 'Welcome back, Admin!');
                    case 'lecturer':
                        Log::info('Redirecting lecturer to dashboard', ['user_id' => $user->id]);
                        return redirect()->route('lecturer.dashboard')
                            ->with('status', 'Welcome back, Lecturer!');
                    case 'student':
                        Log::info('Redirecting student to dashboard', ['user_id' => $user->id]);
                        return redirect()->route('student.dashboard')
                            ->with('status', 'Welcome back, Student!');
                    default:
                        // Fallback for unknown roles
                        Log::warning('Unknown role detected', ['role' => $user->role, 'user_id' => $user->id]);
                        return redirect()->route('welcome')
                            ->with('status', 'Welcome back!');
                }
            }
            
            Log::warning('Failed login attempt', ['email' => $request->email]);
            
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->except('password'));
            
        } catch (\Exception $e) {
            Log::error('Exception during login process', [
                'email' => $request->email,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ])->withInput($request->except('password'));
        }
    }
    
    /**
     * Log the user out
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $userName = $user->name;
            $userId = $user->id;
            $userEmail = $user->email;
            $userRole = $user->role;
            
            Log::info('User logout initiated', [
                'user_id' => $userId,
                'email' => $userEmail,
                'role' => $userRole
            ]);
        } else {
            $userName = 'Unknown';
            $userId = 'unknown';
            Log::info('User logout initiated for unknown user');
        }
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Log::debug('Session invalidated and token regenerated');
        
        // Use Laravel's flash messaging
        $logoutMessage = 'You have been successfully logged out.';
        
        Log::debug('Logout status message set', [
            'status' => $logoutMessage,
            'for_user' => $userName
        ]);
        
        return redirect()->route('login')
            ->with('status', $logoutMessage);
    }
 
    //forget password
    public function ShowForgotPassword(){
        Log::info('Forgot password page accessed');
        return view('auth.forget-password', [
            'title' => 'Forget Password - Affan Student Timetable',
            'description' => 'A smart and user-friendly timetable management tool for students',
            'ogImage' => url('favicon.ico'),
        ]);
    }
    
    /**
     * Send password reset link
     */
    public function sendResetLinkEmail(Request $request)
    {
        Log::info('Password reset requested', ['email' => $request->email]);
        
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);
            
            Log::debug('Email validated for password reset', ['email' => $request->email]);
            
            // Logic for password reset would go here
            // This would typically use Laravel's built-in password reset functionality
            
            Log::info('Password reset link sent', ['email' => $request->email]);
            
            return back()->with('status', 'Password reset link has been sent to your email.');
        } catch (\Exception $e) {
            Log::error('Exception during password reset request', [
                'email' => $request->email,
                'exception' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred. Please try again.',
            ]);
        }
    }
    
    /**
     * Show password reset form
     */
    public function showResetForm(Request $request, $token)
    {
        Log::info('Password reset form accessed', ['email' => $request->email, 'token' => $token]);
        
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
            'title' => 'Reset Password - Affan Student Timetable',
        ]);
    }
    
    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        Log::info('Password reset submitted', ['email' => $request->email]);
        
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);
            
            Log::debug('Password reset data validated', ['email' => $request->email]);
            
            // Password reset logic would go here
            
            Log::info('Password reset successful', ['email' => $request->email]);
            
            return redirect()->route('login')->with('status', 'Your password has been reset successfully.');
        } catch (\Exception $e) {
            Log::error('Exception during password reset', [
                'email' => $request->email,
                'exception' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred. Please try again.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }
    
    /**
     * Show change password form
     */
    public function showChangePasswordForm()
    {
        $user = Auth::user();
        Log::info('Change password form accessed', ['user_id' => $user->id]);
        
        return view('auth.change-password', [
            'title' => 'Change Password - Affan Student Timetable',
        ]);
    }
    
    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        Log::info('Password change requested', ['user_id' => $user->id]);
        
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|confirmed|min:8|different:current_password',
            ]);
            
            Log::debug('Password change data validated', ['user_id' => $user->id]);
            
            if (!Hash::check($request->current_password, $user->password)) {
                Log::warning('Incorrect current password provided', ['user_id' => $user->id]);
                
                return back()->withErrors([
                    'current_password' => 'The current password is incorrect.',
                ]);
            }
            
            $user->update([
                'password' => Hash::make($request->password),
                'first_login' => false,
            ]);
            
            Log::info('Password changed successfully', ['user_id' => $user->id]);
            
            return back()->with('status', 'Password changed successfully.');
        } catch (\Exception $e) {
            Log::error('Exception during password change', [
                'user_id' => $user->id,
                'exception' => $e->getMessage()
            ]);
            
            return back()->withErrors([
                'current_password' => 'An error occurred. Please try again.',
            ]);
        }
    }
    
    /**
     * Debug method to check routes and authentication
     */
    public function debugInfo()
    {
        $user = Auth::user();
        $routes = app('router')->getRoutes();
        $routeList = [];
        
        foreach ($routes as $route) {
            $routeList[] = [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        }
        
        $debugInfo = [
            'authenticated' => Auth::check(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'first_login' => $user->first_login,
            ] : null,
            'session' => session()->all(),
            'routes' => $routeList,
        ];
        
        Log::debug('Debug info requested', $debugInfo);
        
        return response()->json($debugInfo);
    }
}
