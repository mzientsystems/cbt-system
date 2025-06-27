
@extends('layouts.student')

@section('title', 'Taking Exam: ' . $exam->title)

@section('content')
<div class="container-fluid">
    <!-- Timer Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-info">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="text-white mb-0">{{ $exam->title }}</h4>
                            <small class="text-white">Attempt {{ $attempt->attempt_number }} of {{ $exam->max_attempts }}</small>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="text-white">
                                <i class="fas fa-clock"></i>
                                <span id="timer" class="h4">{{ gmdate('H:i:s', $remainingTime) }}</span>
                            </div>
                            <small class="text-white">Time Remaining</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="examForm" action="{{ route('student.exams.submit', $attempt) }}" method="POST">
        @csrf
        <div class="row">
            <!-- Questions Panel -->
            <div class="col-md-9">
                @foreach($questions as $index => $question)
                    <div class="card question-card" id="question-{{ $question->id }}" style="{{ $index == 0 ? '' : 'display: none;' }}">
                        <div class="card-header">
                            <h5>Question {{ $index + 1 }} of {{ count($questions) }}</h5>
                            <div class="card-tools">
                                <span class="badge badge-primary">{{ $question->points }} {{ $question->points == 1 ? 'point' : 'points' }}</span>
                                @if($question->difficulty_level)
                                    <span class="badge badge-{{ $question->difficulty_level == 'easy' ? 'success' : ($question->difficulty_level == 'medium' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($question->difficulty_level) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="question-text mb-4">
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
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $option->id }}"
                                               id="option_{{ $option->id }}"
                                               {{ isset($userAnswers[$question->id]) && $userAnswers[$question->id]->selected_option_id == $option->id ? 'checked' : '' }}>
                                        <label class="form-check-label" for="option_{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                @endforeach
                            @elseif($question->question_type == 'true_false')
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="true"
                                           id="true_{{ $question->id }}"
                                           {{ isset($userAnswers[$question->id]) && $userAnswers[$question->id]->answer_text == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="true_{{ $question->id }}">
                                        True
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="false"
                                           id="false_{{ $question->id }}"
                                           {{ isset($userAnswers[$question->id]) && $userAnswers[$question->id]->answer_text == 'false' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="false_{{ $question->id }}">
                                        False
                                    </label>
                                </div>
                            @elseif($question->question_type == 'essay')
                                <textarea class="form-control" 
                                          name="answers[{{ $question->id }}]" 
                                          rows="6" 
                                          placeholder="Type your answer here...">{{ isset($userAnswers[$question->id]) ? $userAnswers[$question->id]->answer_text : '' }}</textarea>
                            @elseif($question->question_type == 'fill_blank')
                                <input type="text" 
                                       class="form-control" 
                                       name="answers[{{ $question->id }}]" 
                                       placeholder="Type your answer here..."
                                       value="{{ isset($userAnswers[$question->id]) ? $userAnswers[$question->id]->answer_text : '' }}">
                            @endif

                            <div class="mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="mark_for_review[{{ $question->id }}]" 
                                           value="1"
                                           id="review_{{ $question->id }}"
                                           {{ isset($userAnswers[$question->id]) && $userAnswers[$question->id]->marked_for_review ? 'checked' : '' }}>
                                    <label class="form-check-label" for="review_{{ $question->id }}">
                                        <i class="fas fa-flag"></i> Mark for review
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-6">
                                    @if($index > 0)
                                        <button type="button" class="btn btn-secondary" onclick="showQuestion({{ $index - 1 }})">
                                            <i class="fas fa-arrow-left"></i> Previous
                                        </button>
                                    @endif
                                </div>
                                <div class="col-6 text-right">
                                    @if($index < count($questions) - 1)
                                        <button type="button" class="btn btn-primary" onclick="showQuestion({{ $index + 1 }})">
                                            Next <i class="fas fa-arrow-right"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success" onclick="showSubmitModal()">
                                            <i class="fas fa-check"></i> Finish Exam
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Navigation Panel -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h6>Question Navigator</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($questions as $index => $question)
                                <div class="col-3 mb-2">
                                    <button type="button" 
                                            class="btn btn-sm btn-block question-nav-btn" 
                                            id="nav-btn-{{ $index }}"
                                            onclick="showQuestion({{ $index }})">
                                        {{ $index + 1 }}
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <hr>
                        <div class="small">
                            <div class="mb-1"><span class="badge badge-success"></span> Answered</div>
                            <div class="mb-1"><span class="badge badge-warning"></span> Marked for Review</div>
                            <div class="mb-1"><span class="badge badge-secondary"></span> Not Answered</div>
                            <div class="mb-1"><span class="badge badge-primary"></span> Current</div>
                        </div>

                        <hr>
                        <button type="button" class="btn btn-warning btn-block" onclick="showSubmitModal()">
                            <i class="fas fa-check"></i> Submit Exam
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Exam</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit this exam? You cannot change your answers after submission.</p>
                <div id="examSummary"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Continue Exam</button>
                <button type="button" class="btn btn-success" onclick="submitExam()">
                    <i class="fas fa-check"></i> Submit Now
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentQuestion = 0;
let totalQuestions = {{ count($questions) }};
let examTimer;
let remainingTime = {{ $remainingTime }};

$(document).ready(function() {
    startTimer();
    updateNavigationButtons();
    
    // Auto-save answers every 30 seconds
    setInterval(autoSave, 30000);
    
    // Save answer when input changes
    $('input, textarea').on('change', function() {
        autoSave();
        updateNavigationButtons();
    });
});

function startTimer() {
    examTimer = setInterval(function() {
        remainingTime--;
        
        if (remainingTime <= 0) {
            clearInterval(examTimer);
            autoSubmit();
            return;
        }
        
        let hours = Math.floor(remainingTime / 3600);
        let minutes = Math.floor((remainingTime % 3600) / 60);
        let seconds = remainingTime % 60;
        
        $('#timer').text(
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0')
        );
        
        // Warning when 5 minutes left
        if (remainingTime <= 300) {
            $('#timer').addClass('text-danger blink');
        }
    }, 1000);
}

function showQuestion(index) {
    $('.question-card').hide();
    $('#question-' + @json($questions)[index].id).show();
    currentQuestion = index;
    updateNavigationButtons();
}

function updateNavigationButtons() {
    $('.question-nav-btn').removeClass('btn-success btn-warning btn-primary').addClass('btn-secondary');
    
    @foreach($questions as $index => $question)
        let questionId = {{ $question->id }};
        let hasAnswer = false;
        let markedForReview = false;
        
        // Check if question has answer
        @if($question->question_type == 'multiple_choice')
            hasAnswer = $('input[name="answers[' + questionId + ']"]:checked').length > 0;
        @elseif($question->question_type == 'true_false')
            hasAnswer = $('input[name="answers[' + questionId + ']"]:checked').length > 0;
        @else
            hasAnswer = $('input[name="answers[' + questionId + ']"], textarea[name="answers[' + questionId + ']"]').val().trim() !== '';
        @endif
        
        markedForReview = $('input[name="mark_for_review[' + questionId + ']"]').is(':checked');
        
        if ({{ $index }} === currentQuestion) {
            $('#nav-btn-{{ $index }}').addClass('btn-primary');
        } else if (markedForReview) {
            $('#nav-btn-{{ $index }}').addClass('btn-warning');
        } else if (hasAnswer) {
            $('#nav-btn-{{ $index }}').addClass('btn-success');
        }
    @endforeach
}

function autoSave() {
    let formData = new FormData($('#examForm')[0]);
    formData.append('auto_save', '1');
    
    $.ajax({
        url: '{{ route("student.exams.auto-save", $attempt) }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}

function showSubmitModal() {
    let answered = 0;
    let marked = 0;
    
    $('.question-nav-btn').each(function() {
        if ($(this).hasClass('btn-success')) answered++;
        if ($(this).hasClass('btn-warning')) marked++;
    });
    
    let unanswered = totalQuestions - answered;
    
    let summary = '<div class="alert alert-info">';
    summary += '<strong>Exam Summary:</strong><br>';
    summary += 'Total Questions: ' + totalQuestions + '<br>';
    summary += 'Answered: ' + answered + '<br>';
    summary += 'Unanswered: ' + unanswered + '<br>';
    summary += 'Marked for Review: ' + marked;
    summary += '</div>';
    
    if (unanswered > 0) {
        summary += '<div class="alert alert-warning">You have ' + unanswered + ' unanswered questions.</div>';
    }
    
    $('#examSummary').html(summary);
    $('#submitModal').modal('show');
}

function submitExam() {
    clearInterval(examTimer);
    $('#submitModal').modal('hide');
    
    // Show loading
    $('body').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');
    
    $('#examForm').submit();
}

function autoSubmit() {
    alert('Time is up! Your exam will be submitted automatically.');
    submitExam();
}

// Prevent page refresh/close during exam
window.addEventListener('beforeunload', function(e) {
    if (remainingTime > 0) {
        e.preventDefault();
        e.returnValue = 'You have an exam in progress. Are you sure you want to leave?';
        return e.returnValue;
    }
});

// CSS for blinking timer
$('<style>.blink { animation: blink 1s linear infinite; } @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }</style>').appendTo('head');
</script>
@endpush