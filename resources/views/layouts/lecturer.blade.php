
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lecturer Portal') - CBT System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- User Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                             alt="User Avatar" 
                             class="img-circle elevation-2" 
                             style="width: 30px; height: 30px;">
                    @else
                        <i class="fas fa-user-circle fa-lg"></i>
                    @endif
                    <span class="ml-1">{{ auth()->user()->first_name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('lecturer.profile.index') }}" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> My Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('lecturer.dashboard') }}" class="brand-link">
            <i class="fas fa-chalkboard-teacher brand-image"></i>
            <span class="brand-text font-weight-light">CBT Lecturer</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                             class="img-circle elevation-2" 
                             alt="User Image">
                    @else
                        <div class="img-circle elevation-2 bg-secondary d-flex align-items-center justify-content-center" 
                             style="width: 33px; height: 33px;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    @endif
                </div>
                <div class="info">
                    <span class="d-block text-white">
                        {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}
                    </span>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('lecturer.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('lecturer.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-question-circle"></i>
                            <p>
                                Questions
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Question Banks</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>My Questions</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Add Question</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>
                                Exams
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>My Exams</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Create Exam</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Results & Reports</p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('lecturer.profile.index') }}" 
                           class="nav-link {{ request()->routeIs('lecturer.profile.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>My Profile</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; {{ date('Y') }} CBT System.</strong> All rights reserved.
    </footer>
</div>

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

<!-- Common JavaScript -->
<script>
    // CSRF token setup for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
</script>

@stack('scripts')
</body>
</html>

{{-- resources/views/components/exam-timer.blade.php --}}
<div id="exam-timer" class="exam-timer" style="display: none;">
    <i class="fas fa-clock"></i>
    <span id="timer-display">00:00:00</span>
</div>

<script>
class ExamTimer {
    constructor(durationInMinutes, warningTimeInMinutes = 5) {
        this.duration = durationInMinutes * 60; // Convert to seconds
        this.warningTime = warningTimeInMinutes * 60;
        this.timeLeft = this.duration;
        this.timer = null;
        this.isWarning = false;
        
        this.init();
    }
    
    init() {
        this.display();
        this.start();
        document.getElementById('exam-timer').style.display = 'block';
    }
    
    start() {
        this.timer = setInterval(() => {
            this.timeLeft--;
            this.display();
            
            // Warning when time is running out
            if (this.timeLeft <= this.warningTime && !this.isWarning) {
                this.showWarning();
                this.isWarning = true;
            }
            
            // Auto-submit when time is up
            if (this.timeLeft <= 0) {
                this.timeUp();
            }
        }, 1000);
    }
    
    display() {
        const hours = Math.floor(this.timeLeft / 3600);
        const minutes = Math.floor((this.timeLeft % 3600) / 60);
        const seconds = this.timeLeft % 60;
        
        const display = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        document.getElementById('timer-display').textContent = display;
    }
    
    showWarning() {
        const timerEl = document.getElementById('exam-timer');
        timerEl.style.background = '#dc3545';
        timerEl.style.animation = 'blink 1s infinite';
        
        // Add CSS for blinking animation if not exists
        if (!document.getElementById('timer-styles')) {
            const style = document.createElement('style');
            style.id = 'timer-styles';
            style.textContent = `
                @keyframes blink {
                    0%, 50% { opacity: 1; }
                    51%, 100% { opacity: 0.5; }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Show warning modal
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Time Warning!',
                text: `Only ${Math.floor(this.timeLeft / 60)} minutes remaining!`,
                icon: 'warning',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        } else {
            alert(`Warning: Only ${Math.floor(this.timeLeft / 60)} minutes remaining!`);
        }
    }
    
    timeUp() {
        clearInterval(this.timer);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Time Up!',
                text: 'Your exam will be submitted automatically.',
                icon: 'error',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                this.autoSubmit();
            });
        } else {
            alert('Time up! Your exam will be submitted automatically.');
            this.autoSubmit();
        }
    }
    
    autoSubmit() {
        // Auto-submit the exam form
        const examForm = document.getElementById('exam-form');
        if (examForm) {
            // Add a hidden field to indicate time up
            const timeUpField = document.createElement('input');
            timeUpField.type = 'hidden';
            timeUpField.name = 'time_up';
            timeUpField.value = '1';
            examForm.appendChild(timeUpField);
            
            examForm.submit();
        } else {
            // Fallback: redirect to exam submission
            window.location.href = window.location.href.replace('/take/', '/submit/');
        }
    }
    
    stop() {
        if (this.timer) {
            clearInterval(this.timer);
        }
    }
}
</script>

{{-- resources/views/components/question-navigation.blade.php --}}
@props(['questions', 'currentQuestion' => 1, 'answers' => [], 'flagged' => []])

<div class="question-navigation">
    <div class="card">
        <div class="card-header">
            <h5>Question Navigation</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($questions as $index => $question)
                    @php
                        $questionNumber = $index + 1;
                        $isAnswered = isset($answers[$question->id]) && !empty($answers[$question->id]);
                        $isCurrent = $questionNumber == $currentQuestion;
                        $isFlagged = in_array($question->id, $flagged);
                        
                        $btnClass = 'question-nav-btn btn btn-sm';
                        if ($isCurrent) {
                            $btnClass .= ' current';
                        } elseif ($isAnswered) {
                            $btnClass .= ' answered';
                        }
                        if ($isFlagged) {
                            $btnClass .= ' flagged';
                        }
                    @endphp
                    
                    <div class="col-2 mb-2">
                        <button type="button" 
                                class="{{ $btnClass }}" 
                                onclick="goToQuestion({{ $questionNumber }})"
                                title="Question {{ $questionNumber }}{{ $isFlagged ? ' (Flagged)' : '' }}{{ $isAnswered ? ' (Answered)' : '' }}">
                            {{ $questionNumber }}
                        </button>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-3">
                <div class="legend">
                    <small>
                        <span class="badge badge-secondary">Not Answered</span>
                        <span class="badge badge-success">Answered</span>
                        <span class="badge badge-primary">Current</span>
                        <span class="badge badge-warning">Flagged</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function goToQuestion(questionNumber) {
    // Save current answer before navigating
    const currentForm = document.getElementById('exam-form');
    if (currentForm) {
        const formData = new FormData(currentForm);
        const currentQuestionId = document.querySelector('input[name="current_question_id"]').value;
        
        // Auto-save current answer via AJAX
        fetch(`/student/exams/save-answer`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                question_id: currentQuestionId,
                answer: formData.get('answer'),
                selected_option_id: formData.get('selected_option_id'),
                attempt_id: formData.get('attempt_id')
            })
        }).then(() => {
            // Navigate to the selected question
            const currentUrl = window.location.href;
            const newUrl = currentUrl.replace(/\/question\/\d+/, `/question/${questionNumber}`);
            window.location.href = newUrl;
        }).catch(error => {
            console.error('Error saving answer:', error);
            // Navigate anyway
            const currentUrl = window.location.href;
            const newUrl = currentUrl.replace(/\/question\/\d+/, `/question/${questionNumber}`);
            window.location.href = newUrl;
        });
    }
}
</script>