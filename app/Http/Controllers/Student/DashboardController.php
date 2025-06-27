
<?php

// app/Http/Controllers/Student/DashboardController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get available exams
        $availableExams = Exam::where('status', 'published')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->with('subject')
            ->get();

        // Get recent attempts
        $recentAttempts = ExamAttempt::where('user_id', $user->id)
            ->with('exam.subject')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get statistics
        $stats = [
            'total_exams_taken' => ExamAttempt::where('user_id', $user->id)->count(),
            'completed_exams' => ExamAttempt::where('user_id', $user->id)
                ->where('status', 'completed')->count(),
            'average_score' => ExamAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->avg('percentage') ?? 0,
            'available_exams' => $availableExams->count(),
        ];

        return view('student.dashboard', compact('availableExams', 'recentAttempts', 'stats'));
    }
}
