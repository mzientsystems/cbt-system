<?php

// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Email Verification Routes
Auth::routes(['verify' => true]);

// Password Reset Routes
Route::get('/password/change', [App\Http\Controllers\Auth\PasswordController::class, 'showChangeForm'])
    ->name('password.change')
    ->middleware('auth');
Route::post('/password/change', [App\Http\Controllers\Auth\PasswordController::class, 'change'])
    ->middleware('auth');

// Dashboard Routes (Role-based)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        Route::resource('departments', App\Http\Controllers\Admin\DepartmentController::class);
        Route::resource('subjects', App\Http\Controllers\Admin\SubjectController::class);
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
    });
// Admin Routes
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('students', StudentController::class);
    Route::get('students/bulk-import', [StudentController::class, 'bulkImport'])->name('students.bulk-import');
    Route::post('students/bulk-import', [StudentController::class, 'processBulkImport'])->name('students.process-bulk-import');
    Route::get('students/download-template', [StudentController::class, 'downloadTemplate'])->name('students.download-template');
});

    // Lecturer Routes
    Route::middleware(['role:lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'lecturer'])->name('dashboard');
        Route::resource('question-banks', App\Http\Controllers\Lecturer\QuestionBankController::class);
        Route::resource('questions', App\Http\Controllers\Lecturer\QuestionController::class);
        Route::resource('exams', App\Http\Controllers\Lecturer\ExamController::class);
        Route::get('/reports', [App\Http\Controllers\Lecturer\ReportController::class, 'index'])->name('reports');
    });

    // Student Routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');
        Route::get('/exams', [App\Http\Controllers\Student\ExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}', [App\Http\Controllers\Student\ExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/start', [App\Http\Controllers\Student\ExamController::class, 'start'])->name('exams.start');
        Route::get('/attempts/{attempt}', [App\Http\Controllers\Student\ExamController::class, 'take'])->name('attempts.take');
        Route::post('/attempts/{attempt}/submit', [App\Http\Controllers\Student\ExamController::class, 'submit'])->name('attempts.submit');
        Route::get('/results', [App\Http\Controllers\Student\ResultController::class, 'index'])->name('results.index');
    });

    // Student Routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Exams
    Route::get('exams', [StudentExamController::class, 'index'])->name('exams.index');
    Route::get('exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
    Route::post('exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');
    Route::get('exams/take/{attempt}', [StudentExamController::class, 'take'])->name('exams.take');
    Route::post('exams/take/{attempt}/answer', [StudentExamController::class, 'submitAnswer'])->name('exams.submit-answer');
    Route::post('exams/take/{attempt}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
    Route::get('exams/result/{attempt}', [StudentExamController::class, 'result'])->name('exams.result');
    Route::get('exams/review/{attempt}', [StudentExamController::class, 'review'])->name('exams.review');
    
    // Profile
    Route::get('profile', [StudentProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [StudentProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [StudentProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [StudentProfileController::class, 'updatePassword'])->name('profile.update-password');
});


    // Profile Routes (All authenticated users)
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
