@extends('layouts.doctor')

@section('content')
<div class="container py-4">

    {{-- Welcome Section --}}
    <h2 class="mb-1 card-title" data-aos="fade-right">Welcome back, Dr. {{ $doctorUser->name }}!</h2>
    <p class="text-muted mb-4" data-aos="fade-right" data-aos-delay="100">Here's what's happening with your patients today.</p>

    {{-- FIRST ROW: Summary Cards --}}
    <div class="row mb-4">
        {{-- Card 1: Active Follow-ups --}}
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Active Follow-ups</h5>
                        <i class="fas fa-chart-line text-primary fs-4"></i> {{-- Changed to Font Awesome icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $activeFollowUps }}</h2>
                    <p class="card-text text-success small">+2 since yesterday</p> {{-- Static text --}}
                </div>
            </div>
        </div>

        {{-- Card 2: Pending Messages --}}
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Pending Messages</h5>
                        <i class="fas fa-comments text-warning fs-4"></i> {{-- Changed to Font Awesome icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $pendingMessages }}</h2>
                    <p class="card-text text-warning small">3 urgent responses needed</p> {{-- Static text --}}
                </div>
            </div>
        </div>

        {{-- Card 3: Today's Appointments --}}
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="400">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Today's Appointments</h5>
                        <i class="fas fa-calendar-check text-info fs-4"></i> {{-- Changed to Font Awesome icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $todaysAppointments }}</h2>
                    <p class="card-text text-info small">Next one in 45 minutes</p> {{-- Static text --}}
                </div>
            </div>
        </div>

        {{-- Card 4: Total Patients --}}
        <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="500">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Total Patients</h5>
                        <i class="fas fa-users text-secondary fs-4"></i> {{-- Changed to Font Awesome icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalPatients }}</h2>
                    <p class="card-text text-secondary small">+8 this month</p> {{-- Static text --}}
                </div>
            </div>
        </div>
    </div>
    {{-- END Summary Cards Row --}}

    {{-- SECOND ROW: Recent Patients & Upcoming Appointments Tabs --}}
    <div class="row mb-4">
        <div class="col-md-12 mb-4" data-aos="fade-up" data-aos-delay="600">
            <div class="card h-100 animate-card">
                <div class="card-header bg-transparent pb-0 border-0"> {{-- Changed styling for consistency --}}
                    <ul class="nav nav-tabs card-header-tabs" id="doctorDashboardTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="recent-patients-tab" data-bs-toggle="tab" href="#recentPatients" role="tab" aria-controls="recentPatients" aria-selected="true">Recent Patients</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="upcoming-appointments-tab" data-bs-toggle="tab" href="#upcomingAppointments" role="tab" aria-controls="upcomingAppointments" aria-selected="false">Upcoming Appointments</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body pt-3"> {{-- Added pt-3 for padding consistent with patient dashboard --}}
                    <div class="tab-content" id="doctorDashboardTabContent">
                        {{-- Recent Patients Tab Content --}}
                        <div class="tab-pane fade show active" id="recentPatients" role="tabpanel" aria-labelledby="recent-patients-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Patients requiring follow-up care</h5>
                                <a href="{{ route('doctor.assign.form') }}" class="btn btn-primary-filled shadow-sm"> {{-- Changed button style --}}
                                    <i class="fas fa-plus-circle me-2"></i> Add Patient {{-- Changed icon --}}
                                </a>
                            </div>

                            @if ($patients->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Contact</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($patients as $patient)
                                                <tr>
                                                    <td>{{ $patient->user->name }}</td>
                                                    <td>{{ $patient->user->email }}</td>
                                                    <td>{{ $patient->contact_number ?? 'N/A' }}</td>
                                                    <td>
                                                       <a href="{{ route('doctor.patients.show', $patient->id) }}" class="btn btn-sm btn-outline-primary shadow-sm">View Profile</a> {{-- Changed button style --}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-center text-muted py-4">You currently have no patients assigned to you.</p>
                            @endif
                        </div>
                        {{-- END Recent Patients Tab Content --}}
                        {{-- Upcoming Appointments Tab Content --}}
                        <div class="tab-pane fade" id="upcomingAppointments" role="tabpanel" aria-labelledby="upcoming-appointments-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Your schedule for today and tomorrow</h5>
                                <a href="{{ route('doctor.appointments.create') }}" class="btn btn-primary-filled shadow-sm"> {{-- Changed button style --}}
                                    <i class="fas fa-plus-circle me-2"></i> Add Appointment {{-- Changed icon --}}
                                </a>
                            </div>
                            @if ($upcomingAppointments->isNotEmpty())
                                <div class="list-group list-group-flush"> {{-- Added list-group-flush for consistency --}}
                                    @foreach ($upcomingAppointments as $appointment)
                                        <a href="{{ route('doctor.appointments.show', $appointment->id) }}" class="list-group-item d-flex justify-content-between align-items-center py-3"> {{-- Added py-3 for consistency --}}
                                            <div class="d-flex align-items-center">
                                                <img src="https://via.placeholder.com/40" class="rounded-circle me-3" alt="Patient Avatar">
                                                <div>
                                                    <h6 class="mb-0">{{ $appointment->patient->user->name }}</h6>
                                                    <small class="text-muted">{{ $appointment->notes ?? 'No specific notes' }}</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <h6 class="mb-0">{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('h:i A') }}</h6>
                                                <small class="text-muted">
                                                    @if (\Carbon\Carbon::parse($appointment->appointment_datetime)->isToday())
                                                        Today
                                                    @elseif (\Carbon\Carbon::parse($appointment->appointment_datetime)->isTomorrow())
                                                        Tomorrow
                                                    @else
                                                        {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-muted py-4">No upcoming appointments found for today or tomorrow.</p>
                            @endif
                        </div>
                        {{-- END Upcoming Appointments Tab Content --}}
                    </div> {{-- END tab-content --}}
                </div> {{-- END card-body --}}
            </div> {{-- END card mb-4 --}}
        </div> {{-- END col-md-12 --}}
    </div> {{-- END row mb-4 --}}

    {{-- THIRD ROW: Recent Prescriptions --}}
    <div class="row mb-4">
        <div class="col-md-12" data-aos="fade-up" data-aos-delay="700">
            <div class="card h-100 animate-card">
                <div class="card-header bg-transparent pb-0 border-0"> {{-- Changed styling for consistency --}}
                    <h5 class="card-title mb-0">Recent Prescriptions</h5>
                </div>
                <div class="card-body pt-3"> {{-- Added pt-3 for padding consistency --}}
                    @if ($recentPrescriptions->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Drug Name</th>
                                        <th>Dosage</th>
                                        <th>Date Prescribed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentPrescriptions as $prescription)
                                        <tr>
                                            <td>{{ $prescription->patient->user->name }}</td>
                                            <td>{{ $prescription->drug_name }}</td>
                                            <td>{{ $prescription->dosage }}</td>
                                            <td>{{ $prescription->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('doctor.prescriptions') }}" class="btn btn-sm btn-outline-primary shadow-sm">View All</a> {{-- Changed button style --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-4">No recent prescriptions found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- END Recent Prescriptions Table --}}

</div> {{-- END container py-4 --}}
@endsection

{{-- Push custom scripts to the 'scripts' stack in the layout --}}
@push('scripts')
<script>
    // Initialize AOS (Animate On Scroll) if you are using it globally
    // If not, you might need to add the AOS library script in your layout or app.js
    // AOS.init(); // Uncomment if AOS library is included

    // Example of a simple script if needed for the doctor dashboard
    // For now, no specific new JS needed beyond what's handled by CSS and layout JS.
</script>
@endpush