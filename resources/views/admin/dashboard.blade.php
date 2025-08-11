
{{-- resources/views/admin/dashboard.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container py-4">

    {{-- Welcome Section --}}
    <h2 class="mb-1 card-title" data-aos="fade-right">Welcome back, Admin!</h2>
    <p class="text-muted mb-4" data-aos="fade-right" data-aos-delay="100">Here's a quick summary of your system's health and activity.</p>

    {{-- FIRST ROW: Summary Cards --}}
    <div class="row g-4 mb-4">
        {{-- Total Users Card --}}
        <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="200">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Total Users</h5>
                        <i class="fas fa-users text-info fs-4"></i> {{-- Font Awesome Icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalUsers }}</h2>
                    <p class="card-text text-muted small">All registered users</p>
                </div>
            </div>
        </div>

        {{-- Total Patients Card --}}
        <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="300">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Patients</h5>
                        <i class="fas fa-user-injured text-success fs-4"></i> {{-- Font Awesome Icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalPatients }}</h2>
                    <p class="card-text text-muted small">Registered patients</p>
                </div>
            </div>
        </div>

        {{-- Total Doctors Card --}}
        <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="400">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Doctors</h5>
                        <i class="fas fa-user-md text-danger fs-4"></i> {{-- Font Awesome Icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalDoctors }}</h2>
                    <p class="card-text text-muted small">Active medical professionals</p>
                </div>
            </div>
        </div>

        {{-- Approved Caregivers Card --}}
        <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="500">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Approved Caregivers</h5>
                        <i class="fas fa-hand-holding-medical text-primary fs-4"></i> {{-- Font Awesome Icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalApprovedCaregivers }}</h2>
                    <p class="card-text text-muted small">Registered and approved</p>
                </div>
            </div>
        </div>
    </div>
    {{-- END Summary Cards Row --}}

    {{-- SECOND ROW: Activity & Pending Cards --}}
    <div class="row g-4 mb-5">
        {{-- Pending Caregiver Applications Card --}}

<div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="600">
            <a href="{{ route('admin.pending-approvals') }}" class="card h-100 animate-card text-decoration-none text-dark hover-shadow-lg">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Pending Approvals</h5>
                        <i class="fas fa-hourglass-half text-warning fs-4"></i> {{-- Font Awesome Icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalPendingCaregiverApplications }}</h2>
                    <p class="card-text text-muted small">New caregiver applications</p>
                    <small class="text-muted mt-2 d-block">Click to manage applications</small>
                </div>
            </a>
        </div>

        {{-- Total Conversations Card --}}
        <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="700">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Conversations</h5>
                        <i class="fas fa-comments text-secondary fs-4"></i> {{-- Font Awesome Icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalConversations }}</h2>
                    <p class="card-text text-muted small">Total platform discussions</p>
                </div>
            </div>
        </div>

        {{-- Total Messages Card --}}
        <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="800">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Total Messages</h5>
                        <i class="fas fa-envelope-open-text text-info fs-4"></i> {{-- Font Awesome Icon --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalMessages }}</h2>
                    <p class="card-text text-muted small">All messages exchanged</p>
                </div>
            </div>
        </div>

        {{-- Total Appointments Card --}}
        <div class="col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="900">
            <div class="card h-100 animate-card">
                <div class="card-body text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                        <h5 class="card-title mb-0">Appointments</h5>
                        <i class="fas fa-calendar-alt text-success fs-4"></i> {{-- Changed icon color to success --}}
                    </div>
                    <h2 class="fw-bold mb-1">{{ $totalAppointments }}</h2>
                    <p class="card-text text-muted small">Upcoming: {{ $totalUpcomingAppointments }} | Completed: {{ $totalCompletedAppointments }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Users Table --}}
    <div class="row mb-4" data-aos="fade-up" data-aos-delay="1000">
        <div class="col-12">
            <div class="card h-100 animate-card"> {{-- Added animate-card --}}
                <div class="card-header bg-transparent pb-0 border-0"> {{-- Matched Doctor dashboard header style --}}
                    <h4 class="mb-0 text-primary">Latest Registered Users</h4>
</div>
                <div class="card-body pt-3"> {{-- Matched Doctor dashboard body padding --}}
                    @if($latestUsers->isEmpty())
                        <p class="text-center text-muted py-4">No recent user registrations.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Registered On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latestUsers as $index => $user)
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->isPatient())
                                                <span class="badge bg-success">Patient</span>
                                            @elseif($user->isDoctor())
                                                <span class="badge bg-danger">Doctor</span>
                                            @elseif($user->isCaregiver())
                                                <span class="badge bg-primary">Caregiver</span>
                                            @elseif($user->isAdmin())
                                                <span class="badge bg-info">Admin</span>
                                            @else
                                                <span class="badge bg-secondary">User</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row" data-aos="fade-up" data-aos-delay="1100">
        <div class="col-12">
            <div class="card h-100 animate-card"> {{-- Added animate-card --}}
                <div class="card-header bg-transparent pb-0 border-0"> {{-- Matched Doctor dashboard header style --}}
                    <h4 class="card-title mb-0">Quick Actions</h4>
                </div>
                <div class="card-body pt-3"> {{-- Matched Doctor dashboard body padding --}}
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                            <div>
                                <i class="fas fa-user-cog me-2"></i> Manage All Users
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('admin.pending-approvals') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                            <div>


<i class="fas fa-clipboard-check me-2"></i> Review Caregiver Applications ({{ $totalPendingCaregiverApplications }})
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('admin.settings') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                            <div>
                                <i class="fas fa-cogs me-2"></i> System Settings
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div> {{-- END container py-4 --}}
@endsection

@push('scripts')

@endpush