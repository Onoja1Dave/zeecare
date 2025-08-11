@extends('layouts.doctor')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Write New Prescription for {{ $patient->user->name }}</h2>
        <a href="{{ route('doctor.patients.show', $patient->id) }}" class="btn btn-secondary">Back to Patient Profile</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Prescription Details</h5>
        </div>
        <div class="card-body">
            {{-- CORRECTED FORM ACTION HERE: No $patient->id in the route parameters --}}
            <form action="{{ route('doctor.prescriptions.store') }}" method="POST">
                @csrf {{-- Laravel's CSRF protection --}}

                {{-- ADDED THIS CRUCIAL HIDDEN INPUT FOR patient_id --}}
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                <div class="mb-3">
                    <label for="drug_name" class="form-label">Drug Name:</label>
                    <input type="text" class="form-control @error('drug_name') is-invalid @enderror" id="drug_name" name="drug_name" value="{{ old('drug_name') }}" required>
                    @error('drug_name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="dosage" class="form-label">Dosage (e.g., 500mg, 1 tablet):</label>
                    <input type="text" class="form-control @error('dosage') is-invalid @enderror" id="dosage" name="dosage" value="{{ old('dosage') }}" required>
                    @error('dosage')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="frequency" class="form-label">Frequency (e.g., Once a day, Twice daily, Every 8 hours):</label>
                    <input type="text" class="form-control @error('frequency') is-invalid @enderror" id="frequency" name="frequency" value="{{ old('frequency') }}" required>
                    @error('frequency')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label">Duration (in days, e.g., 7, 30):</label>
                    <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration') }}" required min="1">
                    @error('duration')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create Prescription</button>
            </form>
        </div>
    </div>
</div>
@endsection