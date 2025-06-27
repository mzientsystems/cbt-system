
@extends('layouts.student')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if(auth()->user()->avatar)
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                 alt="User profile picture">
                        @else
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ asset('img/default-avatar.png') }}"
                                 alt="User profile picture">
                        @endif
                    </div>

                    <h3 class="profile-username text-center">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h3>

                    <p class="text-muted text-center">
                        @if(auth()->user()->profile && auth()->user()->profile->student_id)
                            Student ID: {{ auth()->user()->profile->student_id }}
                        @endif
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b> <span class="float-right">{{ auth()->user()->email }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Phone</b> <span class="float-right">{{ auth()->user()->phone ?? 'Not provided' }}</span>
                        </li>
                        @if(auth()->user()->profile)
                            <li class="list-group-item">
                                <b>Department</b> 
                                <span class="float-right">
                                    {{ auth()->user()->profile->department->name ?? 'Not assigned' }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Level</b> <span class="float-right">{{ auth()->user()->profile->level ?? 'Not set' }}</span>
                            </li>
                        @endif
                        <li class="list-group-item">
                            <b>Member Since</b> <span class="float-right">{{ auth()->user()->created_at->format('M Y') }}</span>
                        </li>
                    </ul>

                    <a href="{{ route('student.profile.edit') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                </div>
                <div class="card-body">
                    @if($recentAttempts->count() > 0)
                        @foreach($recentAttempts as $attempt)
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <div>
                                    <strong>{{ $attempt->exam->title }}</strong><br>
                                    <small class="text-muted">{{ $attempt->created_at->diffForHumans() }}</small>
                                </div>
                                <div>
                                    @if($attempt->status == 'completed')
                                        <span class="badge badge-{{ $attempt->percentage >= $attempt->exam->pass_mark ? 'success' : 'danger' }}">
                                            {{ number_format($attempt->percentage, 1) }}%
                                        </span>
                                    @else
                                        <span class="badge badge-warning">{{ ucfirst($attempt->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No recent activity</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Statistics -->
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Exams</span>
                            <span class="info-box-number">{{ $statistics['total_exams'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Passed</span>
                            <span class="info-box-number">{{ $statistics['passed_exams'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">In Progress</span>
                            <span class="info-box-number">{{ $statistics['in_progress'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg Score</span>
                            <span class="info-box-number">{{ number_format($statistics['average_score'], 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Performance Over Time</h3>
                </div>
                <div class="card-body">
                    @if($performanceData->count() > 0)
                        <canvas id="performanceChart" height="100"></canvas>
                    @else
                        <p class="text-muted text-center">No performance data available yet.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Exam Results -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Exam Results</h3>
                    <div class="card-tools">
                        <a href="{{ route('student.exams.index') }}" class="btn btn-sm btn-primary">
                            View All Exams
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentResults->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentResults as $attempt)
                                        <tr>
                                            <td>{{ $attempt->exam->title }}</td>
                                            <td>{{ $attempt->exam->subject->name }}</td>
                                            <td>{{ $attempt->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($attempt->status == 'completed')
                                                    {{ $attempt->total_score }}/{{ $attempt->exam->total_marks }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->status == 'completed')
                                                    <span class="badge badge-{{ $attempt->percentage >= $attempt->exam->pass_mark ? 'success' : 'danger' }}">
                                                        {{ $attempt->percentage >= $attempt->exam->pass_mark ? 'Passed' : 'Failed' }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">{{ ucfirst($attempt->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attempt->status == 'completed')
                                                    <a href="{{ route('student.exams.result', $attempt) }}" class="btn btn-xs btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    @if($attempt->exam->allow_review)
                                                        <a href="{{ route('student.exams.review', $attempt) }}" class="btn btn-xs btn-secondary">
                                                            <i class="fas fa-search"></i> Review
                                                        </a>
                                                    @endif
                                                @elseif($attempt->status == 'in_progress')
                                                    <a href="{{ route('student.exams.resume', $attempt) }}" class="btn btn-xs btn-warning">
                                                        <i class="fas fa-play"></i> Resume
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center p-3">No exam results available yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if($performanceData->count() > 0)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($performanceData->pluck('exam_title')),
            datasets: [{
                label: 'Score (%)',
                data: @json($performanceData->pluck('percentage')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush
@endif