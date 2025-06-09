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
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\DB;


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
                        return redirect()->route('student.view-timetable')
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
 
    /**
     * Show forget password form
     */
    public function ShowForgotPassword()
    {
        Log::info('Forgot password page accessed');
        return view('auth.forget-password', [
            'title' => 'Forget Password - Affan Student Timetable',
            'description' => 'Reset your password with OTP verification',
            'ogImage' => url('favicon.ico'),
        ]);
    }

    /**
     * Send password reset link/OTP
     */
    public function sendResetLinkEmail(Request $request)
    {
        Log::info('Password reset requested', ['email' => $request->email]);
        
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);
            
            Log::debug('Email validated for password reset', ['email' => $request->email]);
            
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                Log::warning('Password reset requested for non-existent email', ['email' => $request->email]);
                return back()->withErrors(['email' => 'We could not find a user with that email address.']);
            }
            
            // Generate OTP
            $otp = rand(100000, 999999);
            Log::debug('OTP generated', ['email' => $request->email, 'otp' => $otp]);
            
            // Store OTP in password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($otp),
                    'created_at' => Carbon::now()
                ]
            );
            Log::debug('OTP stored in database', ['email' => $request->email]);
            
            // Send OTP via email
            try {
                Log::debug('Attempting to send OTP email', ['email' => $user->email]);
                
                Mail::to($user->email)->send(new OtpMail($otp));
                
                Log::info('Password reset OTP sent successfully', ['email' => $user->email]);
                
                // Store email in session for the next step
                session(['reset_email' => $user->email]);
                
                // Redirect to OTP verification page instead of returning back
                return redirect()->route('verify.otp.form')
                    ->with('status', 'We have sent an OTP to your email address. Please check your inbox and spam folder.');
                    
            } catch (\Exception $e) {
                Log::error('Failed to send OTP email', [
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return back()->withErrors(['email' => 'Could not send OTP. Please try again later. Error: ' . $e->getMessage()]);
            }
            
        } catch (\Exception $e) {
            Log::error('Exception during password reset request', [
                'email' => $request->email,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred. Please try again. Error: ' . $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Show OTP verification form
     */
    public function showOtpForm()
    {
        if (!session('reset_email')) {
            Log::warning('OTP form accessed without reset_email in session');
            return redirect()->route('recoverPassword');
        }
        
        Log::info('OTP verification form accessed', ['email' => session('reset_email')]);
        
        return view('auth.otp-verification', [
            'title' => 'Verify OTP - Affan Student Timetable',
            'email' => session('reset_email')
        ]);
    }
    
    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);
        
        $email = session('reset_email');
        if (!$email) {
            Log::warning('OTP verification attempted without reset_email in session');
            return redirect()->route('recoverPassword');
        }
        
        Log::info('OTP verification attempted', ['email' => $email, 'otp' => $request->otp]);
        
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();
            
        if (!$passwordReset) {
            Log::warning('OTP verification failed - no reset record found', ['email' => $email]);
            return back()->withErrors(['otp' => 'OTP request not found. Please try again.']);
        }
        
        // Check if OTP is expired (15 minutes)
        if (Carbon::parse($passwordReset->created_at)->addMinutes(15)->isPast()) {
            Log::warning('OTP verification failed - expired OTP', ['email' => $email]);
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }
        
        // Verify OTP
        if (!Hash::check($request->otp, $passwordReset->token)) {
            Log::warning('OTP verification failed - invalid OTP', ['email' => $email, 'provided_otp' => $request->otp]);
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }
        
        Log::info('OTP verified successfully', ['email' => $email]);
        
        // Store token in session for the reset password form
        $token = Str::random(60);
        DB::table('password_reset_tokens')->where('email', $email)->update([
            'token' => Hash::make($token),
            'created_at' => Carbon::now()
        ]);
        
        session(['reset_token' => $token]);
        
        return redirect()->route('password.reset')
            ->with('status', 'OTP verified successfully. Please set your new password.');
    }
    
    /**
     * Show reset password form
     */
    public function showResetForm()
    {
        $email = session('reset_email');
        $token = session('reset_token');
        
        if (!$email || !$token) {
            Log::warning('Reset password form accessed without required session data');
            return redirect()->route('recoverPassword');
        }
        
        Log::info('Reset password form accessed', ['email' => $email]);
        
        return view('auth.reset-password', [
            'title' => 'Reset Password - Affan Student Timetable',
            'email' => $email
        ]);
    }
    
    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);
        
        $email = session('reset_email');
        $token = session('reset_token');
        
        if (!$email || !$token) {
            Log::warning('Password reset attempted without required session data');
            return redirect()->route('recoverPassword');
        }
        
        Log::info('Password reset attempted', ['email' => $email]);
        
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();
            
        if (!$passwordReset || !Hash::check($token, $passwordReset->token)) {
            Log::warning('Password reset failed - invalid or expired token', ['email' => $email]);
            return redirect()->route('recoverPassword')
                ->withErrors(['email' => 'Invalid or expired password reset token.']);
        }
        
        // Update user password
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        
        Log::info('User password updated successfully', ['email' => $email]);
        
        // Delete the password reset record
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        
        // Clear session data
        session()->forget(['reset_email', 'reset_token']);
        
        Log::info('Password reset successful', ['email' => $email]);
        
        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. Please login with your new password.');
    }
    
    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        $email = session('reset_email');
        
        if (!$email) {
            Log::warning('OTP resend attempted without reset_email in session');
            return redirect()->route('recoverPassword');
        }
        
        Log::info('OTP resend requested', ['email' => $email]);
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            Log::warning('OTP resend failed - user not found', ['email' => $email]);
            return redirect()->route('recoverPassword');
        }
        
        // Generate new OTP
        $otp = rand(100000, 999999);
        Log::debug('New OTP generated for resend', ['email' => $email, 'otp' => $otp]);
        
        // Update OTP in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now()
            ]
        );
        
        // Send OTP via email
        try {
            Log::debug('Attempting to resend OTP email', ['email' => $user->email]);
            
            Mail::to($user->email)->send(new OtpMail($otp));
            
            Log::info('Password reset OTP resent successfully', ['email' => $user->email]);
            
            return back()->with('status', 'A new OTP has been sent to your email address.');
                
        } catch (\Exception $e) {
            Log::error('Failed to resend OTP email', [
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['otp' => 'Could not resend OTP. Please try again. Error: ' . $e->getMessage()]);
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
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'current_password' => 'An error occurred. Please try again. Error: ' . $e->getMessage(),
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
            'mail_config' => [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ],
        ];
        
        Log::debug('Debug info requested', $debugInfo);
        
        return response()->json($debugInfo);
    }
}
