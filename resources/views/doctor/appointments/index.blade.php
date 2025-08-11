@extends('layouts.doctor') {{-- Assuming you're using your main layout --}}

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>All Your Appointments</h2>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">List of Appointments</h5>
        </div>
        <div class="card-body">
            @if ($appointments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Date & Time</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->patient->user->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('M d, Y h:i A') }}</td>
                                    <td>{{ $appointment->notes ?? 'No notes' }}</td>
                                    <td>
                                        {{-- DYNAMIC STATUS BADGE --}}
                                        @php
                                            $badgeClass = '';
                                            switch ($appointment->status) {
                                                case 'scheduled':
                                                    $badgeClass = 'bg-info';
                                                    break;
                                                case 'completed':
                                                    $badgeClass = 'bg-success';
                                                    break;
                                                case 'missed':
                                                    $badgeClass = 'bg-warning';
                                                    break;
                                                case 'cancelled':
                                                    $badgeClass = 'bg-danger';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-secondary';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($appointment->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('doctor.patients.show', $appointment->patient->id) }}" class="btn btn-sm btn-info">View Patient</a>
                                        {{-- LINK TO APPOINTMENT DETAIL PAGE FOR STATUS UPDATE --}}
                                        <a href="{{ route('doctor.appointments.show', $appointment->id) }}" class="btn btn-sm btn-primary">Manage</a>
                                    </td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted">You have no appointments scheduled yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection