
@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Welcome, {{ auth()->user()->full_name }}!</h1>
            <p class="text-muted">Student ID: {{ auth()->user()->profile->student_id }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['available_exams'] }}</h3>
                    <p>Available Exams</p>
                </div>
                <div class="icon">
                    <i class="ion ion-document"></i>
                </div>
                <a href="{{ route('student.exams.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['completed_exams'] }}</h3>
                    <p>Completed Exams</p>
                </div>
                <div class="icon">
                    <i class="ion ion-checkmark-round"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($stats['average_score'], 1) }}%</h3>
                    <p>Average Score</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['total_exams_taken'] }}</h3>
                    <p>Total Attempts</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Available Exams -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Available Exams</h3>
                </div>
                <div class="card-body">
                    @if($availableExams->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Subject</th>
                                        <th>Duration</th>
                                        <th>Questions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableExams as $exam)
                                        <tr>
                                            <td>
                                                <strong>{{ $exam->title }}</strong><br>
                                                <small class="text-muted">{{ $exam->exam_type }}</small>
                                            </td>
                                            <td>{{ $exam->subject->name }}</td>
                                            <td>{{ $exam->duration }} minutes</td>
                                            <td>{{ $exam->total_questions }}</td>
                                            <td>
                                                <a href="{{ route('student.exams.show', $exam) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No exams available at the moment.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Attempts -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Attempts</h3>
                </div>
                <div class="card-body">
                    @if($recentAttempts->count() > 0)
                        @foreach($recentAttempts as $attempt)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>{{ $attempt->exam->title }}</strong><br>
                                    <small class="text-muted">{{ $attempt->exam->subject->name }}</small>
                                </div>
                                <div class="text-right">
                                    @if($attempt->percentage !== null)
                                        <span class="badge {{ $attempt->isPassed() ? 'badge-success' : 'badge-danger' }}">
                                            {{ $attempt->percentage }}%
                                        </span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $attempt->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center">
                            <a href="{{ route('student.attempts.index') }}" class="btn btn-sm btn-outline-primary">
                                View All Attempts
                            </a>
                        </div>
                    @else
                        <p class="text-muted">No recent attempts.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection