@extends('layouts.doctor') 

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>All Your Prescriptions</h2>
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
            <h5 class="card-title mb-0">List of Prescriptions</h5>
        </div>
        <div class="card-body">
            @if ($prescriptions->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Drug Name</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                                <th>Duration (Days)</th>
                                <th>Date Prescribed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prescriptions as $prescription)
                                <tr>
                                    <td>{{ $prescription->patient->user->name ?? 'N/A' }}</td>
                                    <td>{{ $prescription->drug_name }}</td>
                                    <td>{{ $prescription->dosage }}</td>
                                    <td>{{ $prescription->frequency }}</td>
                                    <td>{{ $prescription->duration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($prescription->created_at)->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('doctor.patients.show', $prescription->patient->id) }}" class="btn btn-sm btn-info">View Patient</a>
                                        {{-- Add Edit/Delete buttons here later if needed --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted">You have not prescribed any medications yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection