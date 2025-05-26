<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Middleware\CheckRole;

// Guest routes (for unauthenticated users)
Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        // Welcome page
        Route::get('/', 'Welcome')->name('welcome');
        
        // Login routes
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login');
        
        // Password reset routes
        Route::get('/forgot-password', 'ShowForgotPassword')->name('recoverPassword');
        Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
        
        // OTP verification routes
        Route::get('/verify-otp', 'showOtpForm')->name('verify.otp.form');
        Route::post('/verify-otp', 'verifyOtp')->name('verify.otp');
        Route::get('/resend-otp', 'resendOtp')->name('resend.otp');
        
        Route::get('/reset-password', 'showResetForm')->name('password.reset');
        Route::post('/reset-password', 'resetPassword')->name('password.update');
    });
});

// Debug route - only accessible in local environment
if (app()->environment('local')) {
    Route::get('/debug-auth', [AuthController::class, 'debugInfo'])->name('debug.auth');
}

// Auth routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        // Logout route
        Route::post('/logout', 'logout')->name('logout');
        
        // Change password route
        Route::get('/change-password', 'showChangePasswordForm')->name('password.change');
        Route::post('/change-password', 'changePassword')->name('password.change.update');
    });
    
    // Push notification API endpoints (accessible to all authenticated users)
    Route::post('/api/push/subscribe', [PushNotificationController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/api/push/unsubscribe', [PushNotificationController::class, 'unsubscribe'])->name('push.unsubscribe');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware(CheckRole::class . ':admin')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/lecturer', [AdminController::class, 'Lecturer'])->name('lecturer');
            Route::get('/profile', [AdminController::class, 'Profile'])->name('profile');
            Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
            Route::get('/student', [AdminController::class, 'Student'])->name('student');
            Route::get('/create-student', [AdminController::class, 'CreateStudent'])->name('create-student');
            Route::get('/edit-student', [AdminController::class, 'EditStudent'])->name('edit-student');
            
            // Admin Push Notification routes
            Route::get('/push-notifications', [PushNotificationController::class, 'showForm'])->name('push.form');
            Route::post('/api/push/send', [PushNotificationController::class, 'sendNotification'])->name('push.send');
        });
    });
    
    // Lecturer routes
    Route::prefix('lecturer')->name('lecturer.')->group(function () {
        Route::middleware(CheckRole::class . ':lecturer')->group(function () {
            Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('dashboard');
            
            // Lecturer Push Notification routes
            Route::get('/push-notifications', [PushNotificationController::class, 'showForm'])->name('push.form');
            Route::post('/api/push/send', [PushNotificationController::class, 'sendNotification'])->name('push.send');
        });
    });
    
    // Student routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::middleware(CheckRole::class . ':student')->group(function () {
            Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        });
    });
});
