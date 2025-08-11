@extends('layouts.caregiver')

@section('content')
<div class="container py-4">
    {{-- Hero Section: Caregiver Welcome & Illustration --}}
    <div class="caregiver-hero mb-5 shadow-lg rounded-4 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
        <div class="row align-items-center g-0">
            <div class="col-md-8 p-4 p-md-5">
                <h1 class="display-5 fw-bold text-white mb-3 animate-slide-in">Hello, {{ $caregiver->name }}!</h1> {{-- Use $caregiver --}}
                <p class="lead text-white-75 mb-4 animate-slide-in" style="animation-delay: 0.1s;">
                    Welcome back! Here’s a quick glance at your day and key priorities.
                </p>
                <div class="row row-cols-md-auto g-3">
                    <div class="col animate-slide-in" style="animation-delay: 0.2s;">
                        <span class="badge bg-light text-dark fs-6 py-2 px-3 rounded-pill shadow-sm">
                            <i class="fas fa-users me-2"></i> {{ $totalPatients ?? 0 }} Patients
                        </span>
                    </div>
                    <div class="col animate-slide-in" style="animation-delay: 0.3s;">
                        <span class="badge bg-warning text-dark fs-6 py-2 px-3 rounded-pill shadow-sm">
                            <i class="fas fa-bell me-2"></i> {{ $newAlertsCount ?? 0 }} New Alerts
                        </span>
                    </div>
                    <div class="col animate-slide-in" style="animation-delay: 0.4s;">
                        <span class="badge bg-success text-white fs-6 py-2 px-3 rounded-pill shadow-sm">
                            <i class="fas fa-list-check me-2"></i> {{ $pendingTasksCount ?? 0 }} Pending Tasks
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-none d-md-flex align-items-center justify-content-center p-4">
                {{-- Your chosen caregiver illustration goes here --}}
                <img src="{{ asset('images/carephoto2.jpg') }}" alt="Caring Illustration" class="img-fluid caregiver-hero-img" data-aos="zoom-in" data-aos-delay="500">
            </div>
        </div>
    </div>

    {{-- Today's Snapshot / Key Priorities --}}
    <div class="card snapshot-card mb-5 shadow-lg border-0 rounded-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body p-4 p-md-5">
            <h3 class="card-title fw-bold mb-4 text-primary-dark"><i class="fas fa-bullseye me-3"></i>Today's Snapshot</h3>
            <div class="row g-4 align-items-center">
                {{-- Urgent Alerts for Today's Snapshot --}}
                @if(($newAlertsCount ?? 0) > 0)
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 rounded-3 bg-danger-subtle snapshot-item animate-hover-lift">
                        <div class="flex-shrink-0 me-3 text-accent fs-2">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-accent">Urgent Alerts ({{ $newAlertsCount ?? 0 }})</h6>
                            <p class="text-dark mb-0">You have alerts requiring your attention.</p>
                        </div>
                        <a href="#urgent-alerts-section" class="btn btn-sm btn-outline-accent ms-3">View</a> {{-- Link to the section below --}}
                    </div>
                </div>
                @endif

                {{-- Next Task Due Today for Today's Snapshot --}}
                @if($nextTaskToday)
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 rounded-3 bg-primary-subtle snapshot-item animate-hover-lift">
                        <div class="flex-shrink-0 me-3 text-primary fs-2">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-primary">Next Task Due Today</h6>
                            <p class="text-dark mb-0">
                                {{ $nextTaskToday->description }} for {{ $nextTaskToday->patient->user->name ?? 'N/A' }} at {{ $nextTaskToday->due_at->format('h:i A') }}
                            </p>
                        </div>
                        <a href="{{ route('caregiver.tasks.show', $nextTaskToday->id) }}" class="btn btn-sm btn-outline-primary ms-3">View Task</a>
                    </div>
                </div>
                @elseif($todayTasksCount > 0)
                 <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 rounded-3 bg-primary-subtle snapshot-item animate-hover-lift">
                        <div class="flex-shrink-0 me-3 text-primary fs-2">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-primary">Daily Tasks Overview</h6>
                            <p class="text-dark mb-0">You have {{ $todayTasksCount }} tasks today, {{ $todayCompletedTasksCount }} completed.</p>
                        </div>
                        <a href="{{ route('caregiver.tasks') }}" class="btn btn-sm btn-outline-primary ms-3">Go to Tasks</a>
                    </div>
                </div>
                @endif

                {{-- Next Appointment Today for Today's Snapshot --}}
                @if($nextAppointmentToday)
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 rounded-3 bg-info-subtle snapshot-item animate-hover-lift">
                        <div class="flex-shrink-0 me-3 text-info fs-2">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-info">Next Appointment Today</h6>
                            <p class="text-dark mb-0">
                                With {{ $nextAppointmentToday->patient->user->name ?? 'N/A' }} at {{ $nextAppointmentToday->appointment_datetime->format('h:i A') }} ({{ $nextAppointmentToday->type ?? 'Appointment' }})
                            </p>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-info ms-3">View Appt</a>
                    </div>
                </div>
                @elseif($todayAppointmentsCount > 0)
                 <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 rounded-3 bg-info-subtle snapshot-item animate-hover-lift">
                        <div class="flex-shrink-0 me-3 text-info fs-2">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-info">Today's Appointments</h6>
                            <p class="text-dark mb-0">You have {{ $todayAppointmentsCount }} appointments scheduled for today.</p>
                        </div>
                        <a href="{{ route('caregiver.appointments.index') }}" class="btn btn-sm btn-outline-info ms-3">View Appointments</a>
                    </div>
                </div>
                @endif


                {{-- All Clear Message for Today's Snapshot --}}
                @if(($unreadAlertsCount ?? 0) == 0 && !$nextTaskToday && !$nextAppointmentToday && $todayTasksCount == 0 && $todayAppointmentsCount == 0)
                <div class="col-12 text-center py-4">
                    <p class="lead text-muted"><i class="fas fa-check-circle me-2"></i>All clear! No urgent matters or pending tasks/appointments for today.</p>
                    <a href="{{ route('caregiver.patients.index') }}" class="btn btn-outline-primary mt-3">View All Patients</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row mb-5 g-4">
        {{-- Daily Progress Overview --}}
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="dashboard-card text-center p-4 h-100 shadow-lg border-0 rounded-4 animate-hover-lift">
                <h5 class="card-title fw-bold mb-4 text-primary-dark">Daily Task Progress</h5>
                <div class="progress-ring mx-auto mb-3" data-progress="{{ $tasksCompletedTodayPercentage ?? 0 }}"> {{-- Dynamic progress --}}
                    <svg class="progress-ring-svg">
                        <circle class="progress-ring-circle" stroke-width="10" fill="transparent" r="50" cx="60" cy="60"/>
                        <circle class="progress-ring-progress" stroke-width="10" fill="transparent" r="50" cx="60" cy="60"/>
                    </svg>
                    <div class="progress-ring-text">{{ $tasksCompletedTodayPercentage ?? 0 }}%</div>
                </div>
                <p class="card-text text-muted">Tasks Completed Today</p>
                <a href="#" class="stretched-link text-primary text-decoration-none fw-bold mt-2">View Full Log <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>

        {{-- Urgent Messages --}}
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="dashboard-card card-gradient-primary text-white p-4 h-100 shadow-lg border-0 rounded-4 animate-hover-lift">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <i class="fas fa-comments fa-3x text-white-50"></i>
                    <span class="badge bg-light text-dark fs-5 rounded-pill px-3 py-2">{{ $pendingMessagesNav ?? 0 }}</span>
                </div>
                <h5 class="card-title fw-bold">Urgent Messages</h5>
                <p class="card-text opacity-75">Respond to important patient or doctor communications.</p>
                <a href="{{ route('caregiver.messages.index') }}" class="stretched-link text-white text-decoration-none fw-bold">View Messages <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>

        {{-- Upcoming Events --}}
        <div class="col-lg-4 col-md-12" data-aos="fade-up" data-aos-delay="500">
            <div class="dashboard-card card-gradient-accent text-white p-4 h-100 shadow-lg border-0 rounded-4 animate-hover-lift">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <i class="fas fa-calendar-check fa-3x text-white-50"></i>
                    <h5 class="card-title display-6 fw-bold mb-0">{{ $upcomingAppointmentsCount ?? 0 }}</h5> {{-- Dynamic count --}}
                </div>
                <h5 class="card-title fw-bold">Upcoming Events</h5>
                <p class="card-text opacity-75">Don't miss any scheduled visits or meetings.</p>
                <a href="#" class="stretched-link text-white text-decoration-none fw-bold">View Calendar <i class="fas fa-arrow-right ms-2"></i></a> {{-- Corrected link --}}
            </div>
        </div>
    </div>

    {{-- Patient Status At-a-Glance --}}
    <div class="card patient-status-card mb-5 shadow-lg border-0 rounded-4" data-aos="fade-up" data-aos-delay="600">
        <div class="card-body p-4 p-md-5">
            <h3 class="card-title fw-bold mb-4 text-primary-dark"><i class="fas fa-users-medical me-3"></i>My Patients Status</h3>
            <div class="list-group list-group-flush border-0">
                @forelse($patients ?? [] as $patient)
                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center">
                        <div class="patient-status-indicator {{ strtolower(str_replace(' ', '_', $patient->status ?? 'stable')) }} me-3"></div> {{-- Dynamic status class --}}
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">{{ $patient->user->name ?? 'Patient Name' }}</h6> {{-- Access patient's user name --}}
                            <small class="text-muted">Assigned Doctor: {{ $patient->assignedDoctor->user->name ?? 'N/A' }}</small> {{-- Access assigned doctor's user name --}}
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge rounded-pill bg-{{ strtolower(str_replace('_', ' ', $patient->status ?? 'stable')) == 'critical' ? 'danger' : (strtolower(str_replace('_', ' ', $patient->status ?? 'stable')) == 'needs monitoring' ? 'warning' : 'success') }} me-2">
                            {{ ucfirst(str_replace('_', ' ', $patient->status ?? 'Stable')) }}
                        </span>
                        <a href="{{ route('caregiver.patients.show', $patient->id) }}" class="btn btn-sm btn-outline-primary ms-2">View</a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-4">
                    <p class="lead text-muted"><i class="fas fa-user-plus me-2"></i>No patients assigned to you yet.</p>
                    <a href="{{ route('caregiver.patients.create') }}" class="btn btn-outline-primary mt-3">Add Your First Patient</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Urgent Alerts Section (fixed ID for linking) --}}
    <div class="row mb-4" id="urgent-alerts-section">
        <div class="col-md-12">
            <div class="card shadow-sm animate-card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i> Urgent Alerts
                        @if($urgentAlerts->count() > 0)
                            <span class="badge bg-light text-danger rounded-pill ms-2">{{ $urgentAlerts->count() }}</span>
                        @endif
                    </h5>
                    @if($urgentAlerts->count() > 0)
                        <form action="{{ route('caregiver.alerts.mark-all-resolved') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light">Mark All As Resolved</button>
                        </form>
                    @endif
                </div>
                <div class="card-body urgent-alerts-scroll-box">
                    @if ($urgentAlerts->isEmpty())
                        <p class="text-center text-muted py-3 mb-0">No urgent alerts at this time. All good!</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($urgentAlerts as $alert)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 text-danger">
                                            @if($alert->type == 'missed_appointment')
                   <i class="fas fa-calendar-times me-2"></i> Missed Appointment for {{ $alert->patient->user->name }}
                                            @elseif($alert->type == 'missed_medication')
                                                <i class="fas fa-pills me-2"></i> Missed Medication for {{ $alert->patient->user->name }}
                                            @else
                                                <i class="fas fa-bell me-2"></i> {{ ucfirst(str_replace('_', ' ', $alert->type)) }} for {{ $alert->patient->user->name }}
                                            @endif
                                        </h6>
                                        <p class="mb-1">{{ $alert->message }}</p>
                                        <small class="text-muted">Generated: {{ $alert->created_at->diffForHumans() }}</small>
                                    </div>
                                    <form action="{{ route('caregiver.alerts.mark-resolved', $alert->id) }}" method="POST" class="ms-3">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">Mark As Resolved</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Tasks (moved from bottom into a row for better layout) --}}
    <div class="row mb-4"> {{-- New row for tasks (and other potential bottom sections) --}}
        <div class="col-lg-6" data-aos="fade-left" data-aos-delay="800">
            <div class="card shadow-lg border-0 rounded-4 h-100 p-4 bg-primary-subtle animate-card">
                <h4 class="card-title mb-4 fw-bold text-primary"><i class="fas fa-list-check me-2"></i> Pending Tasks</h4>
                <ul class="list-group list-group-flush border-0">
                     @forelse ($pendingTasksList ?? [] as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-bottom py-3">
                        <div>
                            <small class="text-muted">Due: {{ $task->due_at->format('M d, Y h:i A') }}</small><br> {{-- Use full datetime for clarity --}}
                            <span class="fw-bold text-dark">{{ $task->description }} for {{ $task->patient->user->name ?? 'N/A' }}</span> {{-- Access patient user name --}}
                        </div>
                        <a href="{{ route('caregiver.tasks.show', $task->id) }}" class="btn btn-sm btn-outline-primary ms-3">View</a>
                    </li>
                    @empty
                    <li class="list-group-item bg-transparent text-center py-4">
                        <p class="text-muted mb-0"><i class="fas fa-clipboard-check me-2"></i>Great job! No pending tasks right now.</p>
                    </li>
                    @endforelse
                    @if(($pendingTasksCount ?? 0) > 0)
                    <li class="list-group-item bg-transparent text-center pt-4">
                        <a href="{{ route('caregiver.tasks.index') }}" class="btn btn-outline-primary btn-sm px-4">View All Tasks</a> {{-- Corrected route --}}
                    </li>
                    @endif
                </ul>
            </div>
        </div>
                
    </div>

</div>

{{-- This script handles the progress ring animation --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const progressRings = document.querySelectorAll('.progress-ring');

        progressRings.forEach(ring => {
            const radius = ring.querySelector('.progress-ring-circle').r.baseVal;
            const circumference = radius * 2 * Math.PI;
            const progressCircle = ring.querySelector('.progress-ring-progress');

            progressCircle.style.strokeDasharray = ${circumference} ${circumference};
            progressCircle.style.strokeDashoffset = circumference; // Start completely hidden

            const dataProgress = parseFloat(ring.getAttribute('data-progress'));
            const offset = circumference - (dataProgress / 100) * circumference;

            // Animate the progress bar
            setTimeout(() => { // Small delay to ensure CSS is applied
                progressCircle.style.transition = 'stroke-dashoffset 0.8s ease-out';
                progressCircle.style.strokeDashoffset = offset;
            }, 100);

            // Update text
            const progressText = ring.querySelector('.progress-ring-text');
            let currentProgress = 0;
            const animationDuration = 800; // milliseconds
            const startTime = performance.now();

            function animateProgressText(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / animationDuration, 1);
                currentProgress = Math.floor(progress * dataProgress);
                progressText.textContent = ${currentProgress}%;

                if (progress < 1) {
                    requestAnimationFrame(animateProgressText);
                }
            }
            requestAnimationFrame(animateProgressText);
        });
    });
</script>
@endpush

@endsection              