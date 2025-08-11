@extends('layouts.doctor') {{-- Assuming you're using your main layout --}}

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Assign New Patients</h2>
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
            <h5 class="card-title mb-0">Select Patient to Assign</h5>
        </div>
        <div class="card-body">
            @if ($unassignedPatients->isNotEmpty())
                <form action="{{ route('doctor.assign.patient') }}" method="POST">
                    @csrf {{-- Laravel's CSRF protection --}}

                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Choose a Patient:</label>
                        <select class="form-select" id="patient_id" name="patient_id" required>
                            <option value="">-- Select Patient --</option>
                            @foreach ($unassignedPatients as $patient)
                                <option value="{{ $patient->id }}">
                                    {{ $patient->user->name }} ({{ $patient->user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Assign Patient</button>
                </form>
            @else
                <p class="text-center text-muted">No unassigned patients available at the moment.</p>
            @endif
        </div>
    </div>
</div>
@endsection