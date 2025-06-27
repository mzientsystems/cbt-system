
<?php

// app/Http/Controllers/Student/ExamController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $exams = Exam::where('status', 'published')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->with(['subject', 'attempts' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->get();

        return view('student.exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        // Check if exam is available
        if ($exam->status !== 'published' || 
            $exam->start_time > now() || 
            $exam->end_time < now()) {
            return redirect()->route('student.exams.index')
                ->with('error', 'This exam is not available.');
        }

        $user = Auth::user();
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->count();

        // Check max attempts
        if ($attempts >= $exam->max_attempts) {
            return redirect()->route('student.exams.index')
                ->with('error', 'You have reached the maximum number of attempts for this exam.');
        }

        $exam->load('subject');
        
        return view('student.exams.show', compact('exam', 'attempts'));
    }

    public function start(Exam $exam)
    {
        $user = Auth::user();
        
        // Validation checks (same as show method)
        if ($exam->status !== 'published' || 
            $exam->start_time > now() || 
            $exam->end_time < now()) {
            return redirect()->route('student.exams.index')
                ->with('error', 'This exam is not available.');
        }

        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->count();

        if ($attempts >= $exam->max_attempts) {
            return redirect()->route('student.exams.index')
                ->with('error', 'You have reached the maximum number of attempts for this exam.');
        }

        // Check for existing in-progress attempt
        $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('student.exams.take', $existingAttempt);
        }

        DB::beginTransaction();
        
        try {
            // Create new attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'attempt_number' => $attempts + 1,
                'start_time' => now(),
                'status' => 'in_progress',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('student.exams.take', $attempt);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to start exam.');
        }
    }

    public function take(ExamAttempt $attempt)
    {
        // Security checks
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.index')
                ->with('error', 'This exam attempt is no longer active.');
        }

        $exam = $attempt->exam->load(['examQuestions.question.options', 'subject']);
        
        // Check if time limit exceeded
        $timeElapsed = now()->diffInSeconds($attempt->start_time);
        $timeLimit = $exam->duration * 60; // Convert minutes to seconds
        
        if ($timeElapsed >= $timeLimit) {
            // Auto-submit exam
            $this->autoSubmitExam($attempt);
            return redirect()->route('student.exams.result', $attempt)
                ->with('warning', 'Exam was automatically submitted due to time limit.');
        }

        // Get existing answers
        $existingAnswers = ExamAnswer::where('attempt_id', $attempt->id)
            ->pluck('answer_text', 'question_id')
            ->toArray();

        $remainingTime = $timeLimit - $timeElapsed;

        return view('student.exams.take', compact('exam', 'attempt', 'existingAnswers', 'remainingTime'));
    }

    public function submitAnswer(Request $request, ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $questionId = $request->question_id;
        $answer = $request->answer;

        ExamAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'answer_text' => $answer,
                'marked_for_review' => $request->boolean('marked_for_review'),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function submit(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.index')
                ->with('error', 'Unauthorized or invalid attempt.');
        }

        $this->finalizeExam($attempt);

        return redirect()->route('student.exams.result', $attempt)
            ->with('success', 'Exam submitted successfully.');
    }

    private function autoSubmitExam(ExamAttempt $attempt)
    {
        $attempt->update([
            'status' => 'timed_out',
            'end_time' => now(),
            'duration_taken' => now()->diffInSeconds($attempt->start_time),
        ]);

        $this->calculateScore($attempt);
    }

    private function finalizeExam(ExamAttempt $attempt)
    {
        $attempt->update([
            'status' => 'completed',
            'end_time' => now(),
            'duration_taken' => now()->diffInSeconds($attempt->start_time),
        ]);

        $this->calculateScore($attempt);
    }

    private function calculateScore(ExamAttempt $attempt)
    {
        $exam = $attempt->exam;
        $answers = $attempt->answers()->with('question.options')->get();
        
        $totalScore = 0;
        $maxScore = $exam->total_marks;

        foreach ($answers as $answer) {
            $question = $answer->question;
            $points = 0;

            if ($question->question_type === 'multiple_choice') {
                $correctOption = $question->options()->where('is_correct', true)->first();
                if ($correctOption && $answer->answer_text == $correctOption->id) {
                    $points = $question->points;
                }
            } elseif ($question->question_type === 'true_false') {
                $correctOption = $question->options()->where('is_correct', true)->first();
                if ($correctOption && $answer->answer_text == $correctOption->option_text) {
                    $points = $question->points;
                }
            }
            // Essay and fill_blank questions need manual grading

            $answer->update([
                'is_correct' => $points > 0,
                'points_earned' => $points,
            ]);

            $totalScore += $points;
        }

        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        $attempt->update([
            'total_score' => $totalScore,
            'percentage' => round($percentage, 2),
        ]);
    }

    public function result(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $exam = $attempt->exam->load('subject');
        
        if (!$exam->show_results_immediately && $attempt->status === 'completed') {
            return view('student.exams.result-pending', compact('exam', 'attempt'));
        }

        $answers = $attempt->answers()->with('question.options')->get();
        
        return view('student.exams.result', compact('exam', 'attempt', 'answers'));
    }

    public function review(ExamAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $exam = $attempt->exam;
        
        if (!$exam->allow_review) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Review is not allowed for this exam.');
        }

        $exam->load(['examQuestions.question.options', 'subject']);
        $answers = $attempt->answers()->with('question.options')
            ->get()
            ->keyBy('question_id');

        return view('student.exams.review', compact('exam', 'attempt', 'answers'));
    }
}
