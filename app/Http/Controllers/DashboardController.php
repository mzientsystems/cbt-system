<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;

class DashboardController extends Controller
{
    public function admin()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::students()->count(),
            'total_lecturers' => User::lecturers()->count(),
            'total_exams' => Exam::count(),
            'active_exams' => Exam::where('status', 'published')->count(),
            'total_attempts' => ExamAttempt::count(),
            'recent_attempts' => ExamAttempt::with(['user', 'exam'])
                ->latest()
                ->limit(10)
                ->get()
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function lecturer()
    {
        $user = Auth::user();
        $stats = [
            'my_exams' => Exam::where('created_by', $user->id)->count(),
            'my_questions' => Question::where('created_by', $user->id)->count(),
            'total_attempts' => ExamAttempt::whereHas('exam', function($q) use ($user) {
                $q->where('created_by', $user->id);
            })->count(),
            'recent_attempts' => ExamAttempt::with(['user', 'exam'])
                ->whereHas('exam', function($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->latest()
                ->limit(10)
                ->get()
        ];

        return view('lecturer.dashboard', compact('stats'));
    }

    public function student()
    {
        $user = Auth::user();
        $stats = [
            'available_exams' => Exam::where('status', 'published')
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->count(),
            'completed_exams' => ExamAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'total_attempts' => ExamAttempt::where('user_id', $user->id)->count(),
            'recent_attempts' => ExamAttempt::with('exam')
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(),
            'upcoming_exams' => Exam::where('status', 'published')
                ->where('start_time', '>', now())
                ->orderBy('start_time')
                ->limit(5)
                ->get()
        ];

        return view('student.dashboard', compact('stats'));
    }
}