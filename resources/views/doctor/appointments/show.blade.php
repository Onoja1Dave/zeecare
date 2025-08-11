@extends('layouts.doctor') {{-- Or your appropriate doctor layout --}}

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- THIS IS THE LINE I SUSPECT WILL CAUSE THE RELATIONSHIP ERROR IF DATA IS MISSING --}}
        <h2>Appointment Details for Patient: {{ $appointment->patient->user->name ?? 'N/A' }}</h2>
        <a href="{{ route('doctor.appointments.index') }}" class="btn btn-secondary">Back to Appointments</a>
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

    <div class="card mb-4">
        <div class="card-header">
            Appointment Information
        </div>
        <div class="card-body">
            <p><strong>Patient:</strong> {{ $appointment->patient->user->name ?? 'N/A' }}</p>
            <p><strong>Doctor:</strong> {{ $appointment->doctor->name ?? 'N/A' }}</p>
            <p><strong>Date & Time:</strong> {{ $appointment->appointment_datetime->format('M d, Y h:i A') }}</p>
            <p><strong>Notes:</strong> {{ $appointment->notes ?? 'No notes' }}</p>
            <p>
                <strong>Current Status:</strong>
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
                <span class="badge {{ $badgeClass }}">{{ ucfirst($appointment->status ?? 'Unknown') }}</span>
            </p>

            <hr>

            {{-- Form to update status --}}
            @if ($appointment->status != 'completed' && $appointment->status != 'cancelled') {{-- Only show if not already final --}}
                <form action="{{ route('doctor.appointments.updateStatus', $appointment->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH') {{-- Or PUT --}}

                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">Update Status:</label>
                        <select name="status" id="statusSelect" class="form-select w-auto d-inline-block">
                            <option value="">Select New Status</option>
                            <option value="completed">Completed</option>
                            <option value="missed">Missed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-primary ms-2">Update</button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>
@endsection