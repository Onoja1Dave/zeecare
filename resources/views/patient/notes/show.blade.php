}

@extends('layouts.patient') 

@section('content')
<div class="container-fluid py-4"> {{-- Using container-fluid for wider content, adjust as per your layout --}}
    <div class="row">
        <div class="col-lg-9 col-12 mx-auto"> {{-- Centering card on larger screens --}}
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0">Doctor's Note Details</h4>
                        <span class="badge bg-gradient-primary ms-auto">Note ID: {{ $doctorNote->id }}</span>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="p-4">
                        <p class="text-secondary font-weight-bold text-sm">
                            <strong>From:</strong> Dr. {{ $doctorNote->doctor->name ?? 'N/A' }}
                            <br>
                            <strong>Date:</strong> {{ $doctorNote->created_at->format('M d, Y H:i A') }}
                        </p>
                        <hr class="horizontal dark mt-0 mb-3">
                        <h6 class="text-dark">Note Content:</h6>
                        <p class="text-dark">{{ $doctorNote->content }}</p>
                        <hr class="horizontal dark mt-0 mb-3">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('patient.dashboard') }}" class="btn btn-primary btn-sm me-2">Back to Dashboard</a>
                            {{-- You might add a link to print or download here --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection