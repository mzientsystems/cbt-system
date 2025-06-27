
@extends('layouts.admin')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Student Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Student Information -->
                        <div class="col-md-4">
                            <div class="text-center">
                                @if($student->avatar)
                                    <img src="{{ Storage::url($student->avatar) }}" 
                                         alt="Avatar" class="img-circle img-fluid" 
                                         style="width: 150px; height: 150px;">
                                @else
                                    <div class="bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                                         style="width: 150px; height: 150px; color: white; font-size: 3rem;">
                                        {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                    </div>
                                @endif
                                <h3 class="mt-3">{{ $student->full_name }}</h3>
                                <p class="text-muted">{{ $student->profile->student_id }}</p>
                                
                                <span class="badge badge-lg badge-{{ $student->status === 'active' ? 'success' : ($student->status === 'suspended' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Student ID:</th>
                                            <td>{{ $student->profile->student_id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Username:</th>
                                            <td>{{ $student->username }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>{{ $student->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone:</th>
                                            <td>{{ $student->phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department:</th>
                                            <td>{{ $student->profile->department->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Level:</th>
                                            <td>{{ $student->profile->level ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Gender:</th>
                                            <td>{{ $student->profile->gender ? ucfirst($student->profile->gender) : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date of Birth:</th>
                                            <td>{{ $student->profile->date_of_birth ? $student->profile->date_of_birth->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address:</th>
                                            <td>{{ $student->profile->address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email Verified:</th>
                                            <td>
                                                @if($student->email_verified_at)
                                                    <span class="badge badge-success">Verified</span>
                                                @else
                                                    <span class="badge badge-warning">Not Verified</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Last Login:</th>
                                            <td>{{ $student->last_login_at ? $student->last_login_at->diffForHumans() : 'Never' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Joined:</th>
                                            <td>{{ $student->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($student->profile->bio)
                                <div class="mt-3">
                                    <h5>Bio</h5>
                                    <p>{{ $student->profile->bio }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Statistics -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Exam Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Attempts</span>
                                    <span class="info-box-number">{{ $examStats['total_attempts'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed</span>
                                    <span class="info-box-number">{{ $examStats['completed'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Average Score</span>
                                    <span class="info-box-number">{{ number_format($examStats['average_score'], 1) }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-trophy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Best Score</span>
                                    <span class="info-box-number">{{ number_format($examStats['best_score'], 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Exam Attempts -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Recent Exam Attempts</h3>
                </div>
                <div class="card-body">
                    @if($recentAttempts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Subject</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->exam->title }}</td>
                                            <td>{{ $attempt->exam->subject->name }}</td>
                                            <td>
                                                @if($attempt->total_score !== null)
                                                    {{ $attempt->total_score }}/{{ $attempt->exam->total_marks }}
                                                    ({{ number_format($attempt->percentage, 1) }}%)
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $attempt->status === 'completed' ? 'success' : ($attempt->status === 'in_progress' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $attempt->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($attempt->duration_taken)
                                                    {{ gmdate('H:i:s', $attempt->duration_taken) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No exam attempts found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection