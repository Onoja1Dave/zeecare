@extends('layouts.doctor')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Patient Profile: {{ $patient->user->name }}</h2>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Basic Information</h5>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $patient->user->name }}</p>
            <p><strong>Email:</strong> {{ $patient->user->email }}</p>
            <p><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') }}</p>
            <p><strong>Gender:</strong> {{ ucfirst($patient->gender) }}</p>
            <p><strong>Contact:</strong> {{ $patient->contact_number ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $patient->address ?? 'N/A' }}</p>
{{-- Example link from patient profile (e.g., patient show page) --}}
<a href="{{ route('doctor.messages.getOrCreate', ['patient' => $patient->id]) }}" class="btn btn-info">Message Patient</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Medical History</h5>
        </div>
        <div class="card-body">
            <p>{{ $patient->medical_history ?? 'No medical history recorded.' }}</p>
        </div>
    </div>

{{-- This section will now be populated by the generated MedicationDose records --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Medication Doses</h5> {{-- This title is now accurate for the data displayed --}}
        <a href="{{ route('doctor.prescriptions.create', $patient->id) }}" class="btn btn-sm btn-primary">Create New Prescription</a>
    </div>
    <div class="card-body">
        @if ($patient->medicationDoses->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Medication</th>
                            <th>Dosage</th>
                            <th>Scheduled Time</th>
                            <th>Status</th>
                            <th>Taken At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($patient->medicationDoses->sortByDesc('scheduled_at') as $dose)
                            <tr>
                                {{-- These now correctly reference the prescription through the dose --}}
                                <td>{{ $dose->prescription->drug_name ?? 'N/A' }}</td>
                                <td>{{ $dose->prescription->dosage ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($dose->scheduled_at)->format('M d, Y h:i A') }}</td>
                                <td>
                                    <span class="badge {{ $dose->status == 'taken' ? 'bg-success' : ($dose->status == 'scheduled' ? 'bg-info' : 'bg-warning') }}">
                                        {{ ucfirst($dose->status) }}
                                    </span>
                                </td>
                                <td>{{ $dose->taken_at ? \Carbon\Carbon::parse($dose->taken_at)->format('h:i A') : 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-muted">This patient has no medication doses recorded.</p>
        @endif
    </div>
</div>


{{-- NEW SECTION: Appointments --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Appointments</h5>
        </div>
        <div class="card-body">
            @if ($patient->appointments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Notes</th>
                                <th>Doctor</th>
                                <th>Status</th> {{-- Assuming a status or default --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patient->appointments->sortByDesc('appointment_dateTime') as $appointment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_dateTime)->format('M d, Y h:i A') }}</td>
                                    <td>{{ $appointment->notes ?? 'No notes' }}</td>
                                    {{-- We need to eager load the doctor for the appointment if we want the doctor's name --}}
                                    <td>{{ $appointment->doctor->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info">Scheduled</span></td> {{-- Placeholder status, adjust as needed --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted">This patient has no appointments recorded.</p>
            @endif
        </div>
    </div>

{{-- NEW SECTION: Doctor's Notes --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Doctor's Notes</h5>
            </div>
            <div class="card-body">
                {{-- Form to Add New Note --}}
               
                <form action="{{ route('doctor.notes.store') }}" method="POST" class="mb-4">
    @csrf {{-- Make sure @csrf is present --}}

    {{-- This hidden input is CRUCIAL for patient_id --}}
    <input type="hidden" name="patient_id" value="{{ $patient->id }}">

    <div class="mb-3">
        <label for="content" class="form-label">Add a New Note:</label>
        <textarea class="form-control" id="content" name="content" rows="3" placeholder="Type your note here..." required></textarea>
        {{-- Add @error directive if not already there --}}
        @error('content')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Save Note</button>
</form>

                <hr>

               {{-- Display Existing Notes --}}
                @if ($patient->doctorNotes->isNotEmpty())
                    <div class="notes-list">
                        @foreach ($patient->doctorNotes->sortByDesc('created_at') as $note) {{-- Added sortByDesc to show latest first --}}
                            <div class="card mb-3 shadow-sm"> {{-- Added shadow-sm for subtle lift --}}
                                <div class="card-body p-4"> {{-- Increased padding --}}
                                    <div class="d-flex align-items-center mb-3">
                                        {{-- Doctor Avatar (Placeholder for now) --}}
                                        <img src="https://via.placeholder.com/40" class="rounded-circle me-3" alt="Doctor Avatar">
                                        <div>
                                            <h6 class="mb-0">Dr. {{ $note->doctor->name ?? 'Unknown Doctor' }}</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($note->created_at)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($note->created_at)->format('h:i A') }}</small>
                                        </div>
                                    </div>
                                    <p class="card-text mb-3">{{ $note->content }}</p>

                                    {{-- Action Buttons (Acknowledge, Reply) --}}
                                    <div class="d-flex justify-content-start align-items-center pt-2 border-top">
                                        {{-- Acknowledge Button - We'll make this functional next --}}
                                        <button type="button" class="btn btn-light btn-sm me-2">
                                            <i class="bi bi-hand-thumbs-up-fill me-1"></i> Acknowledge
                                        </button>

                                        {{-- Reply Button - Placeholder for now, linked to messaging --}}
                                        <button type="button" class="btn btn-light btn-sm">
                                            <i class="bi bi-chat-dots-fill me-1"></i> Reply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted">No notes recorded for this patient yet.</p>
                @endif




            </div>
        </div>

    {{-- You'll add more sections here: Appointments, Progress Metrics, etc. --}}

</div>
@endsection