@extends('layouts.caregiver')

@section('content')
<div class="container mt-4">
    <h2>Patient Details: {{ $patient->name }}</h2> 

    <div class="card mb-3">
        <div class="card-header">
            Patient Information
        </div>
        <div class="card-body">
            <p><strong>Full Name:</strong> {{ $patient->name }}</p> {{-- ADJUSTED: Use $patient->name --}}
            <p><strong>Contact Number:</strong> {{ $patient->contact_number }}</p> {{-- ADJUSTED: Use $patient->contact_number --}}
            <p><strong>Date of Birth:</strong> {{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') : 'N/A' }}</p>
            <p><strong>Gender:</strong> {{ ucfirst($patient->gender ?? 'N/A') }}</p>
            <p><strong>Medical History:</strong> {{ $patient->medical_history ?? 'N/A' }}</p> {{-- Added medical_history --}}

            @if($patient->assignedDoctor)
                <p><strong>Assigned Doctor:</strong> {{ $patient->assignedDoctor->name }}</p>
            @endif
        </div>
    </div>

    {{-- Tasks and other sections... --}}

    <a href="{{ route('caregiver.dashboard') }}" class="btn btn-secondary mt-3">Back to Patients List</a>
</div>
@endsection