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
        Route::get('/change-password', 'showChangePasswordForm')->name('password.change.form');
        Route::post('/change-password', 'changePassword')->name('password.change');
    });
    
    // Role-specific dashboard routes
    // Admin Dashboard
    Route::get('/admin/dashboard', function () {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Lecturer Dashboard
    Route::get('/lecturer/dashboard', function () {
        if (Auth::user()->role !== 'lecturer') {
            abort(403, 'Unauthorized action.');
        }
        return view('lecturer.dashboard');
    })->name('lecturer.dashboard');
    
    // Student Dashboard
    Route::get('/student/dashboard', function () {
        if (Auth::user()->role !== 'student') {
            abort(403, 'Unauthorized action.');
        }
        return view('student.dashboard');
    })->name('student.dashboard');
});
