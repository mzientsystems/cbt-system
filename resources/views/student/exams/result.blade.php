
@extends('layouts.student')

@section('title', 'Exam Result')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Exam Result: {{ $attempt->exam->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('student.exams.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Exams
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card {{ $attempt->percentage >= $attempt->exam->pass_mark ? 'bg-success' : 'bg-danger' }}">
                                <div class="card-body text-center text-white">
                                    <h1 class="display-4">{{ number_format($attempt->percentage, 1) }}%</h1>
                                    <h4>
                                        @if($attempt->percentage >= $attempt->exam->pass_mark)
                                            <i class="fas fa-check-circle"></i> PASSED
                                        @else
                                            <i class="fas fa-times-circle"></i> FAILED
                                        @endif
                                    </h4>
                                    <p>{{ $attempt->total_score }} out of {{ $attempt->exam->total_marks }} marks</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Exam Details</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Subject:</th>
                                            <td>{{ $attempt->exam->subject->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Attempt:</th>
                                            <td>{{ $attempt->attempt_number }} of {{ $attempt->exam->max_attempts }}</td>
                                        </tr>
                                        <tr>
                                            <th>Time Taken:</th>
                                            <td>{{ gmdate('H:i:s', $attempt->duration_taken) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Started:</th>
                                            <td>{{ $attempt->start_time->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Completed:</th>
                                            <td>{{ $attempt->end_time->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pass Mark:</th>
                                            <td>{{ $attempt->exam->pass_mark }}%</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Performance Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <div class="card bg-info">
                                                <div class="card-body text-white">
                                                    <h3>{{ $summary['total_questions'] }}</h3>
                                                    <p>Total Questions</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="card bg-success">
                                                <div class="card-body text-white">
                                                    <h3>{{ $summary['correct_answers'] }}</h3>
                                                    <p>Correct Answers</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="card bg-danger">
                                                <div class="card-body text-white">
                                                    <h3>{{ $summary['wrong_answers'] }}</h3>
                                                    <p>Wrong Answers</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="card bg-warning">
                                                <div class="card-body text-white">
                                                    <h3>{{ $summary['unanswered'] }}</h3>
                                                    <p>Unanswered</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($attempt->exam->allow_review)
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('student.exams.review', $attempt) }}" class="btn btn-info btn-lg">
                                <i class="fas fa-eye"></i> Review Answers
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($attempt->exam->show_results_immediately && isset($difficultyBreakdown))
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Performance by Difficulty</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($difficultyBreakdown as $difficulty => $data)
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="text-capitalize">{{ $difficulty }} Questions</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="progress mb-2">
                                                            <div class="progress-bar bg-{{ $difficulty == 'easy' ? 'success' : ($difficulty == 'medium' ? 'warning' : 'danger') }}" 
                                                                 style="width: {{ $data['percentage'] }}%">
                                                                {{ number_format($data['percentage'], 1) }}%
                                                            </div>
                                                        </div>
                                                        <small>{{ $data['correct'] }} out of {{ $data['total'] }} correct</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
