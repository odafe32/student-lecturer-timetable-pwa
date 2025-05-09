<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;

// Guest routes (for unauthenticated users)
Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        // Welcome page
        Route::get('/', 'Welcome')->name('welcome');
        
        // Login routes
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login');
        
        // Password reset routes
        Route::get('/forgot', 'ShowForgotPassword')->name('recoverPassword');
        Route::post('/forgot', 'sendResetLinkEmail')->name('password.email');
        Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset');
        Route::post('/reset-password', 'resetPassword')->name('password.update');
    });
});

// Auth routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        // OTP verification routes
        Route::get('/otp', 'Otp')->name('verify-account');
        Route::post('/verify-otp', 'verifyOtp')->name('verify.otp');
        Route::get('/resend-otp', 'resendOtp')->name('resend.otp');
        
        // Logout route
        Route::post('/logout', 'logout')->name('logout');
        
        // Change password route
        Route::get('/change-password', 'showChangePasswordForm')->name('password.change.form');
        Route::post('/change-password', 'changePassword')->name('password.change');
    });
    
    // Role-specific dashboard routes
    Route::middleware('verified.otp')->group(function () {
        // Admin routes
        Route::middleware('role:admin')->group(function () {
            Route::get('/admin/dashboard', function () {
                return view('admin.dashboard');
            })->name('admin.dashboard');
        });
        
        // Lecturer routes
        Route::middleware('role:lecturer')->group(function () {
            Route::get('/lecturer/dashboard', function () {
                return view('lecturer.dashboard');
            })->name('lecturer.dashboard');
        });
        
        // Student routes
        Route::middleware('role:student')->group(function () {
            Route::get('/student/dashboard', function () {
                return view('student.dashboard');
            })->name('student.dashboard');
        });
    });
});
