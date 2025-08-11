{{-- resources/views/doctor/patients/index.blade.php --}}
@extends('layouts.doctor')

@section('content')
<div class="container py-4">

   
  

    {{-- Patients List Card --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100 animate-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header bg-transparent pb-0 border-0">
                    <h5 class="card-title mb-0">All Assigned Patients</h5>
                    <small class="text-muted">Manage your patient list</small>
                </div>
                <div class="card-body pt-3">
                    @if ($patients->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle"> {{-- Added align-middle for better vertical alignment --}}
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Contact</th>
                                        <th scope="col">Last Appointment</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($patients as $patient)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{-- Placeholder for patient avatar --}}
                                                    <img src="https://via.placeholder.com/40/{{ substr(md5($patient->user->email ?? 'default'), 0, 6) }}/ffffff?text={{ strtoupper(substr($patient->user->name ?? 'P', 0, 1)) }}" class="rounded-circle me-2" alt="Avatar">
                                                    <div>
                                                        <strong class="text-color">{{ $patient->user->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $patient->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $patient->user->email }}</td>
                                            <td>{{ $patient->contact_number ?? 'N/A' }}</td>
                                            <td>
                                                @if($patient->appointments->isNotEmpty())
                                                    {{ \Carbon\Carbon::parse($patient->appointments->first()->appointment_datetime)->format('M j, Y') }}
                                                @else
                                                    No appointments
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('doctor.patients.show', $patient->id) }}" class="btn btn-sm btn-outline-primary shadow-sm me-2">View Profile</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-circle me-2"></i> No patients found assigned to your account.
                            <br>
                            Click "Assign New Patient" to get started!
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Initialize AOS if you have it setup in app.js or your layout
    // AOS.init(); // Uncomment if using AOS and it's included
</script>
@endpush