
@extends('layouts.student')

@section('title', 'Available Exams')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Available Exams</h3>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search exams..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="subject" class="form-control">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                            {{ request('subject') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="exam_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="practice" {{ request('exam_type') == 'practice' ? 'selected' : '' }}>Practice</option>
                                <option value="assessment" {{ request('exam_type') == 'assessment' ? 'selected' : '' }}>Assessment</option>
                                <option value="final" {{ request('exam_type') == 'final' ? 'selected' : '' }}>Final</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </form>

                    <!-- Exams List -->
                    <div class="row">
                        @forelse($exams as $exam)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ $exam->title }}</h5>
                                        <small class="text-muted">{{ $exam->subject->name }}</small>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">{{ Str::limit($exam->description, 100) }}</p>
                                        
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-clock text-primary"></i> <strong>Duration:</strong> {{ $exam->duration }} minutes</li>
                                            <li><i class="fas fa-question-circle text-info"></i> <strong>Questions:</strong> {{ $exam->total_questions }}</li>
                                            <li><i class="fas fa-star text-warning"></i> <strong>Total Marks:</strong> {{ $exam->total_marks }}</li>
                                            <li><i class="fas fa-check-circle text-success"></i> <strong>Pass Mark:</strong> {{ $exam->pass_mark }}</li>
                                        </ul>

                                        <div class="mb-2">
                                            <span class="badge badge-{{ $exam->exam_type === 'practice' ? 'info' : ($exam->exam_type === 'assessment' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($exam->exam_type) }}
                                            </span>
                                        </div>

                                        @php
                                            $userAttempts = $exam->attempts()->where('user_id', auth()->id())->count();
                                            $canTakeExam = $userAttempts < $exam->max_attempts;
                                            $timeRemaining = $exam->end_time > now();
                                        @endphp

                                        @if(!$timeRemaining)
                                            <div class="alert alert-danger">
                                                <small>This exam has expired.</small>
                                            </div>
                                        @elseif(!$canTakeExam)
                                            <div class="alert alert-warning">
                                                <small>Maximum attempts reached ({{ $userAttempts }}/{{ $exam->max_attempts }})</small>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <small>Attempts: {{ $userAttempts }}/{{ $exam->max_attempts }}</small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        @if($canTakeExam && $timeRemaining)
                                            <a href="{{ route('student.exams.start', $exam) }}" 
                                               class="btn btn-success btn-sm float-right"
                                               onclick="return confirm('Are you sure you want to start this exam?')">
                                                <i class="fas fa-play"></i> Start Exam
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <h4><i class="fas fa-info-circle"></i> No Exams Available</h4>
                                    <p>There are currently no exams available for you to take.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($exams->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $exams->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
