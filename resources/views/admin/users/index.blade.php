ALEX, [19/07/2025 04:02]
{{-- resources/views/admin/users/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container py-4">

    {{-- Page Header --}}
    <h2 class="mb-1 card-title" data-aos="fade-right">Manage Users</h2>
    <p class="text-muted mb-4" data-aos="fade-right" data-aos-delay="100">Oversee and manage all registered users on the platform.</p>

    {{-- User List Card --}}
    <div class="card shadow-sm animate-card" data-aos="fade-up" data-aos-delay="200">
        <div class="card-header bg-transparent pb-0 border-0 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-primary">All Platform Users</h4>
            {{-- Optional: Add User Button (e.g., for creating new users manually) --}}
            {{-- <a href="{{ route('admin.users.create') }}" class="btn btn-primary-filled shadow-sm">
                <i class="fas fa-plus-circle me-2"></i> Add New User
            </a> --}}
        </div>
        <div class="card-body pt-3">
            @if ($users->isEmpty())
                <p class="text-center text-muted py-4">No users found on the platform.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->isAdmin())
                                            <span class="badge bg-info">Admin</span>
                                        @elseif($user->isDoctor())
                                            <span class="badge bg-danger">Doctor</span>
                                        @elseif($user->isPatient())
                                            <span class="badge bg-success">Patient</span>
                                        @elseif($user->isCaregiver())
                                            <span class="badge bg-primary">Caregiver</span>
                                        @else
                                            <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Display status based on user_type and where status data lives --}}
                                        @if ($user->isDoctor())
                                            {{-- Doctor status is on the User model itself --}}
                                            <span class="badge {{ $user->status == 'approved' ? 'bg-success' : ($user->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        @elseif ($user->isCaregiver() && $user->caregiver)
                                            {{-- Caregiver status is on the related Caregiver model --}}
                                            <span class="badge {{ $user->caregiver->status == 'approved' ? 'bg-success' : ($user->caregiver->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">

ALEX, [19/07/2025 04:02]
{{ ucfirst($user->caregiver->status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary shadow-sm me-2" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete Button (with confirmation) --}}
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" title="Delete User">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination (if you implement pagination in controller) --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // AOS should already be initialized in layouts/admin.blade.php if enabled globally.
    // No specific JS for this page beyond what Bootstrap handles for tables.
</script>
@endpush