@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Your Notifications</h2>
        <a href="{{ route('home') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    @if ($notifications->isNotEmpty())
        <div class="list-group">
            @foreach ($notifications as $notification)
                <a href="{{ isset($notification->data['link']) ? $notification->data['link'] : '#' }}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-start {{ is_null($notification->read_at) ? 'bg-light' : '' }}"
                   aria-current="true">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">
                            @if ($notification->type == 'App\Notifications\NewDoctorNote')
                                <i class="bi bi-journal-medical text-primary me-2"></i> New Doctor's Note
                            @else
                                <i class="bi bi-info-circle text-info me-2"></i> General Update
                            @endif
                        </div>
                        <p class="mb-1">
                            @if ($notification->type == 'App\Notifications\NewDoctorNote')
                                Dr. {{ $notification->data['doctor_name'] ?? 'Unknown' }} added a note for you:
                                "{{ $notification->data['note_content_snippet'] ?? 'No snippet available.' }}"
                            @else
                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                            @endif
                        </p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                        @if (!is_null($notification->read_at))
                            <br><small class="text-success">Read: {{ \Carbon\Carbon::parse($notification->read_at)->diffForHumans() }}</small>
                        @endif
                    </div>
                    @if (is_null($notification->read_at))
                        <span class="badge bg-primary rounded-pill">New</span>
                    @endif
                </a>
            @endforeach
        </div>
    @else
        <p class="text-center text-muted">You have no notifications.</p>
    @endif
</div>
@endsection