
@extends('layouts.student')

@section('title', 'Review Exam')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Review: {{ $attempt->exam->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('student.exams.result', $attempt) }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Result
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Score:</strong> {{ $attempt->total_score }}/{{ $attempt->exam->total_marks }} ({{ number_format($attempt->percentage, 1) }}%)</p>
                            <p><strong>Time Taken:</strong> {{ gmdate('H:i:s', $attempt->duration_taken) }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group btn-group-sm mb-2" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="filterQuestions('all')">All</button>
                                <button type="button" class="btn btn-outline-success" onclick="filterQuestions('correct')">Correct</button>
                                <button type="button" class="btn btn-outline-danger" onclick="filterQuestions('wrong')">Wrong</button>
                                <button type="button" class="btn btn-outline-warning" onclick="filterQuestions('unanswered')">Unanswered</button>
                            </div>
                        </div>
                    </div>

                    @foreach($questions as $index => $question)
                        @php
                            $userAnswer = $answers->where('question_id', $question->id)->first();
                            $isCorrect = $userAnswer && $userAnswer->is_correct;
                            $isAnswered = $userAnswer && ($userAnswer->selected_option_id || $userAnswer->answer_text);
                        @endphp

                        <div class="card mb-3 review-question" data-status="{{ $isCorrect ? 'correct' : ($isAnswered ? 'wrong' : 'unanswered') }}">
                            <div class="card-header {{ $isCorrect ? 'bg-success' : ($isAnswered ? 'bg-danger' : 'bg-warning') }}">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0 text-white">
                                            Question {{ $index + 1 }} 
                                            @if($isCorrect)
                                                <i class="fas fa-check-circle"></i>
                                            @elseif($isAnswered)
                                                <i class="fas fa-times-circle"></i>
                                            @else
                                                <i class="fas fa-exclamation-circle"></i>
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge badge-light">
                                            {{ $userAnswer ? $userAnswer->points_earned : 0 }}/{{ $question->points }} points
                                        </span>
                                        @if($question->difficulty_level)
                                            <span class="badge badge-light">
                                                {{ ucfirst($question->difficulty_level) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="question-text mb-3">
                                    {!! nl2br(e($question->question_text)) !!}
                                    
                                    @if($question->image_path)
                                        <div class="mt-3">
                                            <img src="{{ asset('storage/' . $question->image_path) }}" 
                                                 alt="Question Image" 
                                                 class="img-fluid" 
                                                 style="max-height: 300px;">
                                        </div>
                                    @endif
                                </div>

                                @if($question->question_type == 'multiple_choice')
                                    @foreach($question->options as $option)
                                        @php
                                            $isSelected = $userAnswer && $userAnswer->selected_option_id == $option->id;
                                            $isCorrectOption = $option->is_correct;
                                        @endphp
                                        
                                        <div class="form-check mb-2 {{ $isCorrectOption ? 'bg-light-success' : '' }} {{ $isSelected && !$isCorrectOption ? 'bg-light-danger' : '' }}" 
                                             style="padding: 8px; border-radius: 4px;">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   disabled
                                                   {{ $isSelected ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                {{ $option->option_text }}
                                                @if($isCorrectOption)
                                                    <i class="fas fa-check text-success ml-2"></i>
                                                @endif
                                                @if($isSelected && !$isCorrectOption)
                                                    <i class="fas fa-times text-danger ml-2"></i>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach

                                @elseif($question->question_type == 'true_false')
                                    @php
                                        $correctAnswer = $question->options->where('is_correct', true)->first();
                                        $userAnswerText = $userAnswer ? $userAnswer->answer_text : null;
                                    @endphp
                                    
                                    <div class="form-check mb-2 {{ $correctAnswer && $correctAnswer->option_text == 'true' ? 'bg-light-success' : '' }} {{ $userAnswerText == 'true' && (!$correctAnswer || $correctAnswer->option_text != 'true') ? 'bg-light-danger' : '' }}" 
                                         style="padding: 8px; border-radius: 4px;">
                                        <input class="form-check-input" type="radio" disabled {{ $userAnswerText == 'true' ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            True
                                            @if($correctAnswer && $correctAnswer->option_text == 'true')
                                                <i class="fas fa-check text-success ml-2"></i>
                                            @endif
                                            @if($userAnswerText == 'true' && (!$correctAnswer || $correctAnswer->option_text != 'true'))
                                                <i class="fas fa-times text-danger ml-2"></i>
                                            @endif
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2 {{ $correctAnswer && $correctAnswer->option_text == 'false' ? 'bg-light-success' : '' }} {{ $userAnswerText == 'false' && (!$correctAnswer || $correctAnswer->option_text != 'false') ? 'bg-light-danger' : '' }}" 
                                         style="padding: 8px; border-radius: 4px;">
                                        <input class="form-check-input" type="radio" disabled {{ $userAnswerText == 'false' ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            False
                                            @if($correctAnswer && $correctAnswer->option_text == 'false')
                                                <i class="fas fa-check text-success ml-2"></i>
                                            @endif
                                            @if($userAnswerText == 'false' && (!$correctAnswer || $correctAnswer->option_text != 'false'))
                                                <i class="fas fa-times text-danger ml-2"></i>
                                            @endif
                                        </label>
                                    </div>

                                @elseif($question->question_type == 'essay')
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Your Answer:</strong></label>
                                        <div class="form-control" style="min-height: 100px; background-color: #f8f9fa;">
                                            {{ $userAnswer ? $userAnswer->answer_text : 'No answer provided' }}
                                        </div>
                                    </div>

                                @elseif($question->question_type == 'fill_blank')
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Your Answer:</strong></label>
                                        <div class="form-control {{ $isCorrect ? 'border-success' : 'border-danger' }}" style="background-color: #f8f9fa;">
                                            {{ $userAnswer ? $userAnswer->answer_text : 'No answer provided' }}
                                        </div>
                                    </div>
                                @endif

                                @if($question->explanation)
                                    <div class="alert alert-info mt-3">
                                        <h6><i class="fas fa-lightbulb"></i> Explanation:</h6>
                                        {!! nl2br(e($question->explanation)) !!}
                                    </div>
                                @endif

                                @if($userAnswer && $userAnswer->marked_for_review)
                                    <div class="mt-2">
                                        <span class="badge badge-warning">
                                            <i class="fas fa-flag"></i> Marked for Review
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-light-success {
    background-color: rgba(40, 167, 69, 0.1) !important;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
    border: 1px solid rgba(220, 53, 69, 0.2);
}
</style>
@endpush

@push('scripts')
<script>
function filterQuestions(status) {
    $('.btn-group .btn').removeClass('active');
    $('button[onclick="filterQuestions(\'' + status + '\')"]').addClass('active');
    
    if (status === 'all') {
        $('.review-question').show();
    } else {
        $('.review-question').hide();
        $('.review-question[data-status="' + status + '"]').show();
    }
}

$(document).ready(function() {
    // Set default filter
    filterQuestions('all');
});
</script>
@endpush