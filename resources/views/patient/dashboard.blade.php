@extends('layouts.patient')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            {{-- Session Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show animateanimated animatefadeInDown" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show animateanimated animatefadeInDown" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Welcome Header & Quick Actions --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 animateanimated animatefadeIn">
                <div>
                    <h2 class="display-6 fw-bold mb-1">Welcome back, {{ $user->name }}!</h2>
                    <p class="lead text-muted mb-0">Here's your health summary for today.</p>
                </div>
                <div class="d-flex align-items-center mt-3 mt-md-0">
                    {{-- Notification Bell Icon --}}
                    <a href="{{ route('patient.notifications.index') }}" class="btn btn-light btn-lg rounded-circle position-relative me-3 dashboard-action-btn shadow-sm" title="Notifications">
                        <i class="fas fa-bell"></i>
                        @if ($unreadNotificationsCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadNotificationsCount }}
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        @endif
                    </a>

                   {{-- Message your Doctor Icon --}}
<a href="{{ route('patient.messages.index') }}" class="btn btn-primary btn-lg rounded-circle dashboard-action-btn shadow-sm" title="Message your Doctor">
    <i class="fas fa-comment-dots"></i>
    {{-- ADD THIS NEW SECTION BELOW --}}
    @if ($unreadMessagesCount > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            {{ $unreadMessagesCount }}
            <span class="visually-hidden">unread messages</span>
        </span>
    @endif
</a>
                </div>
            </div>

            {{-- FIRST ROW: Key Health Overview (Follow-up Status, Medication Adherence, Next Appointment) --}}
            <div class="row mb-4">
                {{-- Follow-up Status Card --}}
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 animate-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Follow-up Status</h5>
                                <i class="fas fa-chart-line text-info fs-4"></i>
                            </div>
                             <p class="card-text">{{ $followUpStatusText }}</p>
    <p class="card-text">{{ $followUpDetailsText }}</p>
                        </div>
                    </div>
                </div>

                {{-- Medication Adherence Card --}}
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 animate-card">
                        <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                            <h5 class="card-title mb-3">Medication Adherence</h5>
                            <div class="progress-ring mb-3" data-progress="75"> {{-- Example: 75% adherence --}}
                                <svg class="progress-ring-svg">
                                    <circle class="progress-ring-circle" cx="60" cy="60" r="50"></circle>
                                    <circle class="progress-ring-progress" cx="60" cy="60" r="50"></circle>
                                </svg>
                                <span class="progress-ring-text fw-bold">75%</span>
                            </div>
                            <p class="card-text text-muted">You've taken 75% of your medications this week</p> {{-- Update dynamically --}}
                        </div>
                    </div>
                </div>

                {{-- Next Appointment Card --}}
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100 animate-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Next Appointment</h5>
                                <i class="fas fa-calendar-alt text-success fs-4"></i>
                            </div>
                            @if ($nextAppointment)
                                <h3 class="fw-bold mb-2">{{ \Carbon\Carbon::parse($nextAppointment->appointment_datetime)->format('M j, Y') }}</h3>
                                <p class="card-text text-muted">{{ \Carbon\Carbon::parse($nextAppointment->appointment_datetime)->format('h:i A') }} with Dr. {{ $nextAppointment->doctor->name ?? 'N/A' }}</p>
                            @else
                                <h3 class="fw-bold mb-2 text-muted">No upcoming</h3>
                                <p class="card-text text-muted">appointments scheduled.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>



{{-- SECOND ROW: Today's Medications & Recovery Progress --}}
<div class="row mb-4">
    {{-- Today's Medications Card --}}
    <div class="col-md-7 mb-4" data-aos="fade-up" data-aos-delay="400">
        <div class="card h-100 animate-card">
            <div class="card-header bg-transparent pb-0 border-0">
                <h5 class="card-title mb-0">Today's Medications</h5>
                <small class="text-muted">Remember to take your medications on time</small>
            </div>
            <div class="card-body pt-3">
                @if ($todayMedications->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach ($todayMedications as $medication) {{-- Assuming $medication here is a MedicationDose object --}}
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <i class="fas fa-capsules text-info me-2"></i>
                                    <strong>{{ $medication->prescription->drug_name }}</strong>
                                    @if ($medication->dose)
                                        <small class="text-muted ms-1">({{ $medication->dose }})</small>
                                    @endif
                                    <br>
                                    <span class="text-muted ms-4 fw-light">{{ \Carbon\Carbon::parse($medication->scheduled_at)->format('h:i A') }}</span>
                                </div>
                                <div>
                                    {{-- Replaced Checkbox with Button --}}
                                    @if (!$medication->is_taken)
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm mark-as-taken-btn"
                                            data-dose-id="{{ $medication->id }}" {{-- Pass the ID of the MedicationDose --}}
                                        >
                                            Mark as Taken
                                        </button>
                                    @else
                                        <span class="text-success">Taken at {{ \Carbon\Carbon::parse($medication->taken_at)->format('h:i A') }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-center text-muted py-4">No medications scheduled for today. Good job!</p>
                @endif
            </div>
        </div>
    </div>





                {{-- Recovery Progress Card --}}
                <div class="col-md-5 mb-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="card h-100 animate-card">
                        <div class="card-header bg-transparent pb-0 border-0">
                            <h5 class="card-title mb-0">Recovery Progress</h5>
                            <small class="text-muted">Track your health metrics against targets</small>
                        </div>
                        <div class="card-body pt-3">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-heartbeat text-danger me-2"></i> Blood Pressure</span>
                                    @if ($latestBloodPressure)
                                        <span class="fw-bold">{{ $latestBloodPressure->systolic }} / {{ $latestBloodPressure->diastolic }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-weight text-secondary me-2"></i> Weight</span>
                                    @if ($latestWeight)
                                        <span class="fw-bold">{{ $latestWeight->value }} {{ $latestWeight->unit }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-walking text-success me-2"></i> Daily Steps</span>
                                    @if ($latestSteps)
                                        <span class="fw-bold">{{ $latestSteps->count }} steps</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                        @endif
                                </li>
                            </ul>
                            <div class="text-center mt-4">
                                <button class="btn btn-primary-filled shadow-sm"><i class="fas fa-plus-circle me-2"></i>Add New Measurement</button>
                            </div>
                            {{-- Placeholder for a Chart.js graph --}}
                            {{-- <canvas id="recoveryProgressChart" class="mt-4"></canvas> --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- THIRD ROW: Doctor's Notes & Upcoming Appointments --}}
            <div class="row mb-4">
                {{-- Doctor's Notes Card --}}
                <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="card h-100 animate-card">
                        <div class="card-header bg-transparent pb-0 border-0">
                            <h5 class="card-title mb-0">Doctor's Notes</h5>
                            <small class="text-muted">Latest feedback from your healthcare provider</small>
                        </div>
                        <div class="card-body pt-3">
                            @if ($doctorNotes->isNotEmpty())
                                <ul class="list-group list-group-flush">
                                   
                                    @foreach ($doctorNotes->slice(0, 3) as $note)
                                        <li class="list-group-item py-3">
                                            <p class="mb-1">
                                                <i class="fas fa-user-md text-primary me-2"></i>
                                                <strong>Dr. {{ $note->doctor->name ?? 'N/A' }}</strong>
                                                <small class="text-muted ms-2">- {{ \Carbon\Carbon::parse($note->created_at)->diffForHumans() }}</small>
                                            </p>
                                            <p class="mb-1">{{ $note->content }}</p>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-outline-primary me-2 shadow-sm">Acknowledge</button>
                                                <button class="btn btn-sm btn-primary shadow-sm">Reply</button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-center text-muted py-4">No doctor's notes available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Upcoming Appointments Card --}}
                <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="700">
                    <div class="card h-100 animate-card">
                        <div class="card-header bg-transparent pb-0 border-0">
                            <h5 class="card-title mb-0">Upcoming Appointments</h5>
                            <small class="text-muted">Your scheduled visits with healthcare providers</small>
                        </div>
                        <div class="card-body pt-3">
                            @if ($allAppointments->isNotEmpty())
                                <ul class="list-group list-group-flush">
                                    @foreach ($allAppointments as $appointment)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                            <div>
                                                <i class="fas fa-calendar-check text-success me-2"></i>
                                              <strong>Dr. {{ $appointment->doctor->name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted ms-4">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M j, Y') }} at {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('h:i A') }}</small>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-2 shadow-sm">Reschedule</button>
                                                <button class="btn btn-sm btn-primary shadow-sm">Details</button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-center text-muted py-4">No upcoming appointments.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOURTH ROW: Recent Notifications --}}
            <div class="row mb-4" data-aos="fade-up" data-aos-delay="800">
                <div class="col-md-12">
                    <div class="card h-100 animate-card">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center pb-0 border-0">
                            <h5 class="card-title mb-0">Recent Notifications</h5>
                            @if ($notifications->isNotEmpty())
                                <a href="{{ route('patient.notifications.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">View All</a>
                            @endif
                        </div>
                        <div class="card-body pt-3">
                            @if ($notifications->isNotEmpty())
                                <ul class="list-group list-group-flush">
                                    @foreach ($notifications->take(5) as $notification)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 {{ is_null($notification->read_at) ? 'unread-notification-item' : '' }}">
                                            <div>
                                                @php
                                                    $notificationData = $notification->data;
                                                    $iconClass = 'fas fa-info-circle text-info'; // Default icon
                                                    if (isset($notificationData['type'])) {
                                                        if ($notificationData['type'] == 'doctor_note_added') {
                                                            $iconClass = 'fas fa-notes-medical text-primary';
                                                        } elseif ($notificationData['type'] == 'appointment_scheduled') {
                                                            $iconClass = 'fas fa-calendar-check text-success';
                                                        }
                                                    }
                                                @endphp
                                                <i class="{{ $iconClass }} me-2"></i>
                                                @if ($notificationData['type'] == 'doctor_note_added')
                                                    A new note has been added by Dr. {{ $notificationData['doctor_name'] ?? 'Unknown' }} for you.
                                                    <br>
                                                      <small class="text-muted ms-4">
                                                        "{{ Str::limit($notificationData['note_content_snippet'] ?? 'No snippet available.', 70) }}"
                                                    </small>
                                                @elseif (isset($notificationData['message']))
                                                    {{ $notification->data['message'] }}
                                                @else
                                                    New Notification
                                                @endif
                                                <br>
                                                <small class="text-muted ms-4 fw-light">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                            </div>
                                            <div>
                                                @if (is_null($notification->read_at))
                                                    @if ($notificationData['type'] == 'doctor_note_added' && isset($notificationData['link']))
                                                        <a href="{{ $notificationData['link'] }}" class="btn btn-sm btn-info shadow-sm">View Note</a>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary mark-notification-read shadow-sm" data-id="{{ $notification->id }}">Mark as Read</button>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary opacity-75 py-2 px-3">Read</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-center text-muted py-4">No new notifications.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Define csrfToken here to make it accessible to all scripts if not already in your layout head
    // (Recommended: place <meta name="csrf-token" content="{{ csrf_token() }}"> in your <head> and retrieve it once)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // --- Notification Mark as Read Logic ---
    const markNotificationReadButtons = document.querySelectorAll('.mark-notification-read');
    const markReadBaseUrl = "{{ route('patient.notifications.mark-read', ['notification' => 'NotificationIdPlaceholder']) }}";

    markNotificationReadButtons.forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            const finalUrl = markReadBaseUrl.replace('NotificationIdPlaceholder', notificationId);

            fetch(finalUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        // CORRECTED LINE: Added '+' for string concatenation
                        throw new Error(errorData.message + ' Failed to mark notification as read.');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log(data.message);
                    location.reload(); // Reload to reflect read status
                } else {
                    alert('Error marking notification as read: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error marking notification as read.');
            });
        });
    });

    // --- Dark Mode Toggle Logic (NEW) ---
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;
    const lightIcon = darkModeToggle.querySelector('.light-icon');
    const darkIcon = darkModeToggle.querySelector('.dark-icon');

    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        lightIcon.classList.add('d-none');
        darkIcon.classList.remove('d-none');
    }

    darkModeToggle.addEventListener('click', function() {
        if (body.classList.contains('dark-mode')) {
            body.classList.remove('dark-mode');
            lightIcon.classList.remove('d-none');
            darkIcon.classList.add('d-none');
            localStorage.setItem('theme', 'light');
        } else {
            body.classList.add('dark-mode');
            lightIcon.classList.add('d-none');
            darkIcon.classList.remove('d-none');
            localStorage.setItem('theme', 'dark');
        }
    });

    // --- Progress Ring Initialization (NEW) ---
    document.querySelectorAll('.progress-ring').forEach(ringContainer => {
        const circle = ringContainer.querySelector('.progress-ring-progress');
        const radius = circle.r.baseVal.value;
        const circumference = 2 * Math.PI * radius;
        // CORRECTED LINES: Used backticks () for template literals
        circle.style.strokeDasharray = ${circumference} ${circumference}`;
        circle.style.strokeDashoffset = circumference; // Start hidden

        const progress = ringContainer.dataset.progress;
        const offset = circumference - (progress / 100) * circumference;
        circle.style.strokeDashoffset = offset;

        // Optional: Add a transition for animated progress
        circle.style.transition = 'stroke-dashoffset 0.8s ease-out';
        // Update text
        const textSpan = ringContainer.querySelector('.progress-ring-text');
        // CORRECTED LINE: Used backticks () for template literals
        textSpan.textContent = ${progress}%`;
    });
</script>

<script>

           document.addEventListener('DOMContentLoaded', function() {
    // Select all buttons with the class 'mark-as-taken-btn'
    document.querySelectorAll('.mark-as-taken-btn').forEach(button => {
        button.addEventListener('click', function() {
            const doseId = this.dataset.doseId; // Get the medication dose ID from the data attribute
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const currentButton = this; // Store a reference to the clicked button

            // Disable the button immediately to prevent multiple clicks
            currentButton.disabled = true;
            currentButton.textContent = 'Processing...';

            fetch(`/patient/medication-doses/${doseId}/mark-taken`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ is_taken: true }) // A button usually means "mark as taken" (true)
            })
            .then(response => {
                // IMPORTANT: This block checks for the 302 redirect issue we've been debugging
                if (!response.ok && response.status === 302) {
                     console.error('API call redirected unexpectedly (Status 302). Check server config for redirects.');
                     alert('Failed to mark dose as taken: Unexpected redirect. Please try again or contact support.');
                     return Promise.reject('Redirected'); // Stop processing with a custom error
                }
                // Also check if fetch followed a redirect to an unexpected URL (e.g., dashboard)
                if (!response.url.includes(`/patient/medication-doses/${doseId}/mark-taken`)) {
                    console.error('API call followed redirect to: ', response.url);
                    alert('Failed to mark dose as taken: Response from unexpected URL after fetch. Please try again or contact support.');
                    return Promise.reject('Unexpected URL after fetch'); // Stop processing
                }
                return response.json(); // Parse the JSON response
            })
            .then(data => {
                if (data.success) {
                    const now = new Date();
                    const takenAtTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    // Replace the button with a success message and the time it was marked
                    currentButton.replaceWith(document.createTextNode(`Taken at ${takenAtTime}`));
                    alert(data.message); // Show the success message from the server
                } else {
                    // Re-enable button and show error if API call was not successful
                    currentButton.disabled = false;
                    currentButton.textContent = 'Mark as Taken'; // Revert button text
                    alert('Failed to mark dose as taken: ' + (data.message || 'Unknown error.'));
                }
            })
            .catch(error => {
                console.error('Error marking dose as taken:', error);
                // Re-enable button and show generic error on network or JS error
                currentButton.disabled = false;
                currentButton.textContent = 'Mark as Taken'; // Revert button text
                if (error === 'Redirected' || error === 'Unexpected URL after fetch') {
                    // These are custom errors for the 302 issue, already handled in alert
                } else {
                    alert('An error occurred while updating the dose status. Please try again.');
                }
            });
        });
    });
});

</script>
@endpush