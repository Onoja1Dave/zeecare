@extends('layouts.patient') {{-- This line tells it to use your patient layout --}}

@section('content') {{-- All your page content goes inside this section --}}
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Your Appointments</h4>
        </div>

        <div class="card-body">
            @if ($appointments->isEmpty())
                <div class="alert alert-info" role="alert">
                    <p class="mb-0">You have no upcoming appointments.</p>
                    <p class="mb-0">For new appointments, please contact your doctor or administrator.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Doctor</th>
                                <th>Reason / Notes</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->appointment_datetime->format('Y-m-d H:i') }}</td>
                                    <td>
                                        {{-- Ensure your Appointment model has a 'doctor' relationship --}}
                                        {{ $appointment->doctor->name ?? 'N/A' }}
                                    </td>
                                    <td>{{ $appointment->reason ?? 'No reason provided.' }}</td>
                                    <td>
                                        <span class="badge {{
                                            $appointment->status == 'pending' ? 'bg-warning text-dark' :
                                            ($appointment->status == 'confirmed' ? 'bg-success' :
                                            ($appointment->status == 'cancelled' ? 'bg-danger' : 'bg-secondary'))
                                        }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="mt-4">
                    {{ $appointments->links() }}
                </div>
            @endif

            <div class="mt-3">
                <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection {{-- End of the content section --}}