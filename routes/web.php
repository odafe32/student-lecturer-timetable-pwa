<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TimetableController;
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
    Route::get('/vapid-public-key', [PushNotificationController::class, 'getVapidPublicKey']);
    
    // Subscribe to push notifications
    Route::post('/push-subscriptions', [PushNotificationController::class, 'subscribe']);
    
    // Unsubscribe from push notifications
    Route::delete('/push-subscriptions', [PushNotificationController::class, 'unsubscribe']);
    
    // Test notification (optional - for testing)
    Route::post('/test-notification', [PushNotificationController::class, 'testNotification']);

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
            Route::get('/profile', [AdminController::class, 'Profile'])->name('profile');
            Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

            // Lecturer Management Routes
            Route::get('/lecturer', [AdminController::class, 'Lecturer'])->name('lecturer');
            Route::get('/lecturers', [AdminController::class, 'Lecturer'])->name('lecturers.index'); // Alternative route name
            Route::get('/create-lecturer', [AdminController::class, 'createLecturer'])->name('create-lecturer');
            Route::get('/lecturers/create', [AdminController::class, 'createLecturer'])->name('lecturers.create'); // Alternative route name
            Route::post('/store-lecturer', [AdminController::class, 'storeLecturer'])->name('store-lecturer');
            Route::post('/lecturers', [AdminController::class, 'storeLecturer'])->name('lecturers.store'); // Alternative route name
            Route::get('/view-lecturer/{id}', [AdminController::class, 'viewLecturer'])->name('view-lecturer');
            Route::get('/lecturers/{id}', [AdminController::class, 'viewLecturer'])->name('lecturers.show'); // Alternative route name
            Route::get('/edit-lecturer/{id}', [AdminController::class, 'editLecturer'])->name('edit-lecturer');
            Route::get('/lecturers/{id}/edit', [AdminController::class, 'editLecturer'])->name('lecturers.edit'); // Alternative route name
            Route::put('/update-lecturer/{id}', [AdminController::class, 'updateLecturer'])->name('update-lecturer');
            Route::put('/lecturers/{id}', [AdminController::class, 'updateLecturer'])->name('lecturers.update'); // Alternative route name
            Route::delete('/delete-lecturer/{id}', [AdminController::class, 'deleteLecturer'])->name('delete-lecturer');
            Route::delete('/lecturers/{id}', [AdminController::class, 'deleteLecturer'])->name('lecturers.destroy'); // Alternative route name

            // Student management routes
            Route::get('/student', [AdminController::class, 'student'])->name('student');
            Route::get('/students', [AdminController::class, 'student'])->name('students.index'); // Alternative route name
            Route::get('/create-student', [AdminController::class, 'createStudent'])->name('create-student');
            Route::get('/students/create', [AdminController::class, 'createStudent'])->name('students.create'); // Alternative route name
            Route::post('/store-student', [AdminController::class, 'storeStudent'])->name('store-student');
            Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store'); // Alternative route name
            Route::get('/view-student/{id}', [AdminController::class, 'viewStudent'])->name('view-student');
            Route::get('/students/{id}', [AdminController::class, 'viewStudent'])->name('students.show'); // Alternative route name
            Route::get('/edit-student/{id}', [AdminController::class, 'editStudent'])->name('edit-student');
            Route::get('/students/{id}/edit', [AdminController::class, 'editStudent'])->name('students.edit'); // Alternative route name
            Route::put('/update-student/{id}', [AdminController::class, 'updateStudent'])->name('update-student');
            Route::put('/students/{id}', [AdminController::class, 'updateStudent'])->name('students.update'); // Alternative route name
            Route::delete('/delete-student/{id}', [AdminController::class, 'deleteStudent'])->name('delete-student');
            Route::delete('/students/{id}', [AdminController::class, 'deleteStudent'])->name('students.destroy'); // Alternative route name

            // AJAX Routes
            Route::get('/api/departments', [AdminController::class, 'getDepartments'])->name('get-departments');

            // Admin Push Notification routes
            Route::get('/push-notifications', [PushNotificationController::class, 'showForm'])->name('push.form');
            Route::post('/api/push/send', [PushNotificationController::class, 'sendNotification'])->name('push.send');
        });
    });

    // Lecturer routes
    Route::prefix('lecturer')->name('lecturer.')->group(function () {
        Route::middleware(CheckRole::class . ':lecturer')->group(function () {
            // Dashboard routes
            Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('dashboard');
            Route::get('/dashboard/stats', [LecturerController::class, 'dashboardStats']);
            
            // Timetable routes
            Route::get('/timetable/sessions', [TimetableController::class, 'getSessionDetails']);
            Route::get('/timetable/upcoming', [TimetableController::class, 'getUpcomingClasses']);
            Route::post('/timetable/{timetable}/mark-completed', [TimetableController::class, 'markSessionCompleted']);
            Route::get('/timetable/{timetable}/details', [TimetableController::class, 'getTimetableDetails']);
            Route::get('/timetable/export-pdf', [TimetableController::class, 'exportPdf']);
            Route::get('/timetable/monthly-weekly-schedule', [TimetableController::class, 'getMonthlyWeeklySchedule']);
            Route::get('/timetable/current-week', [TimetableController::class, 'getCurrentWeekSchedule']);
            
            // Messages routes
            Route::get('/messages/recent', [LecturerController::class, 'getRecentMessages']);
            Route::get('/messages', [LecturerController::class, 'messages'])->name('messages');
            Route::post('/messages', [LecturerController::class, 'sendMessage'])->name('messages.send');
            Route::get('/messages/{id}', [LecturerController::class, 'viewMessage'])->name('messages.view');
            Route::delete('/messages/{id}', [LecturerController::class, 'deleteMessage'])->name('messages.delete');
            
            // AJAX routes for messages
            Route::get('/api/departments-for-messages', [LecturerController::class, 'getDepartmentsByFacultyForMessages'])
                ->name('api.departments-for-messages');
            Route::get('/api/levels-by-department', [LecturerController::class, 'getLevelsByDepartment'])
                ->name('api.levels-by-department');
            
            // Profile routes
            Route::get('/profile', [LecturerController::class, 'profile'])->name('profile');
            Route::put('/profile', [LecturerController::class, 'updateProfile'])->name('profile.update');
            Route::post('/profile/image', [LecturerController::class, 'updateProfileImage'])->name('profile.image');
            Route::put('/profile/password', [LecturerController::class, 'updatePassword'])->name('password.update');

            // Timetable main views
            Route::get('/time-table', [TimetableController::class, 'index'])->name('time-table');
            Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable'); // Added alias
            Route::get('/time-table/create', [TimetableController::class, 'create'])->name('timetable.create');
            Route::post('/time-table', [TimetableController::class, 'store'])->name('timetable.store');
            Route::get('/time-table/{timetable}/edit', [TimetableController::class, 'edit'])->name('timetable.edit');
            Route::put('/time-table/{timetable}', [TimetableController::class, 'update'])->name('timetable.update');
            Route::delete('/time-table/{timetable}', [TimetableController::class, 'destroy'])->name('timetable.destroy');
            Route::get('/time-table/export-pdf', [TimetableController::class, 'exportPdf'])->name('timetable.export-pdf');

            // AJAX endpoints - these must match exactly with the JavaScript fetch URLs
            Route::get('/time-table/departments/{faculty_id}', [TimetableController::class, 'getDepartmentsByFaculty'])
                ->name('timetable.departments');
            Route::get('/time-table/courses/{department_id}', [TimetableController::class, 'getCoursesByDepartment']);
            Route::post('/time-table/check-conflict', [TimetableController::class, 'checkConflict']);

            // Lecturer Push Notification routes
            Route::get('/push-notifications', [PushNotificationController::class, 'showForm'])->name('push.form');
            Route::post('/api/push/send', [PushNotificationController::class, 'sendNotification'])->name('push.send');
        });
    });
// Student routes
// Student routes
Route::prefix('student')->name('student.')->group(function () {
    Route::middleware(CheckRole::class . ':student')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/messages', [StudentController::class, 'messages'])->name('messages');
        Route::get('/messages/{id}', [StudentController::class, 'viewMessage'])->name('messages.view');
        Route::post('/messages/{id}/read', [StudentController::class, 'markMessageAsRead'])->name('messages.read');
        Route::post('/messages/toggle-read/{id}', [StudentController::class, 'toggleMessageReadStatus'])->name('messages.toggle-read');
        
        // Timetable routes
        Route::get('/view-timetable', [StudentController::class, 'timetable'])->name('view-timetable');
        Route::get('/timetable/by-day', [StudentController::class, 'getTimetableByDay'])->name('timetable.by-day');
        Route::get('/timetable/month-data', [StudentController::class, 'getMonthData'])->name('timetable.month-data');
        Route::get('/timetable/week-data', [StudentController::class, 'getWeekData'])->name('timetable.week-data');
        Route::get('/timetable/export-pdf', [StudentController::class, 'exportPdf'])->name('timetable.export-pdf');
        
        // Profile routes
        Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/image', [StudentController::class, 'updateProfileImage'])->name('profile.image');
        Route::put('/profile/password', [StudentController::class, 'updatePassword'])->name('password.update');

        
        // *** NEW: Push notification routes ***
        Route::post('/push/subscribe', [StudentController::class, 'subscribeToPushNotifications'])->name('push.subscribe');
        Route::post('/push/unsubscribe', [StudentController::class, 'unsubscribeFromPushNotifications'])->name('push.unsubscribe');
        Route::get('/notifications/preferences', [StudentController::class, 'getNotificationPreferences'])->name('notifications.preferences');
        Route::get('/notifications/unread-count', [StudentController::class, 'getUnreadMessageCount'])->name('notifications.unread-count');
        Route::get('/notifications/today-classes', [StudentController::class, 'getTodayClasses'])->name('notifications.today-classes');

    });
});
});