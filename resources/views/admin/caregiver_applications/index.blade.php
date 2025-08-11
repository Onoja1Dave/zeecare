{{-- resources/views/admin/caregiver_applications/index.blade.php --}}

@extends('layouts.caregiver')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Caregiver Applications</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Back to Admin Dashboard</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($applications->isEmpty())
        <p class="text-center text-muted">No caregiver applications found.</p>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact Info</th>
                        <th>Experience</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $application)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $application->name }}</td>
                            <td>{{ $application->email }}</td>
                            <td>{{ $application->contact_info }}</td>
                            <td>{{ Str::limit($application->experience, 50) }}</td>
                            <td>{{ Str::limit($application->reason, 50) }}</td>
                            <td>
                                <span class="badge bg-{{
                                    $application->status == 'pending' ? 'warning' :
                                    ($application->status == 'approved' ? 'success' : 'danger')
                                }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td>{{ $application->created_at->format('M d, Y') }}</td>
                            <td>
                                @if ($application->status == 'pending')
                                    <form action="{{ route('admin.caregiver_applications.approve', $application->id) }}" method="POST" class="d-inline me-1">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.caregiver_applications.reject', $application->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">No actions</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection