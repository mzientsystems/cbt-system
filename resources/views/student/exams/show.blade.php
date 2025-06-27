
@extends('layouts.student')

@section('title', 'Exam Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $exam->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('student.exams.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Exams
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Exam Information</h4>
                            
                            @if($exam->description)
                                <p>{{ $exam->description }}</p>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Subject:</th>
                                            <td>{{ $exam->subject->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Duration:</th>
                                            <td>{{ $exam->duration }} minutes</td>
                                        </tr>
                                        <tr>
                                            <th>Total Questions:</th>
                                            <td>{{ $exam->total_questions }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Marks:</th>
                                            <td>{{ $exam->total_marks }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Pass Mark:</th>
                                            <td>{{ $exam->pass_mark }}</td>
                                        </tr>
                                        <tr>
                                            <th>Exam Type:</th>
                                            <td>
                                                <span class="badge badge-{{ $exam->exam_type == 'final' ? 'danger' : ($exam->exam_type == 'assessment' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($exam->exam_type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Start Time:</th>
                                            <td>{{ $exam->start_time->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>End Time:</th>
                                            <td>{{ $exam->end_time->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Max Attempts:</th>
                                            <td>{{ $exam->max_attempts }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($exam->instructions)
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle"></i> Instructions</h5>
                                    {!! nl2br(e($exam->instructions)) !!}
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Exam Status</h5>
                                </div>
                                <div class="card-body">
                                    @if($canTakeExam)
                                        @if($remainingAttempts > 0)
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle"></i> 
                                                You can take this exam
                                            </div>
                                            <p><strong>Attempts remaining:</strong> {{ $remainingAttempts }}</p>
                                            
                                            @if($exam->start_time <= now() && $exam->end_time >= now())
                                                <a href="{{ route('student.exams.start', $exam) }}" 
                                                   class="btn btn-success btn-block">
                                                    <i class="fas fa-play"></i> Start Exam
                                                </a>
                                            @elseif($exam->start_time > now())
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-clock"></i> 
                                                    Exam starts on {{ $exam->start_time->format('M d, Y h:i A') }}
                                                </div>
                                            @else
                                                <div class="alert alert-danger">
                                                    <i class="fas fa-times-circle"></i> 
                                                    Exam has ended
                                                </div>
                                            @endif
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                No attempts remaining
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-danger">
                                            <i class="fas fa-ban"></i> 
                                            You cannot take this exam
                                        </div>
                                    @endif

                                    @if($userAttempts->count() > 0)
                                        <hr>
                                        <h6>Previous Attempts</h6>
                                        @foreach($userAttempts as $attempt)
                                            <div class="small mb-2">
                                                <strong>Attempt {{ $attempt->attempt_number }}:</strong>
                                                @if($attempt->status == 'completed')
                                                    <span class="text-success">{{ $attempt->percentage }}%</span>
                                                    @if($exam->allow_review)
                                                        <a href="{{ route('student.exams.review', $attempt) }}" class="btn btn-xs btn-outline-info ml-1">
                                                            Review
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ ucfirst($attempt->status) }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection