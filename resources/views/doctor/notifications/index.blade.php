

@extends('layouts.doctor')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Your Notifications</h2>
        <div>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <form action="{{ route('doctor.notifications.markAllRead') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary me-2">Mark All As Read</button>
                </form>
            @endif
            <a href="{{ route('doctor.dashboard') }}" class="btn btn-secondary btn-sm">Back to Dashboard</a>
        </div>
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

    @if ($notifications->isNotEmpty())
        <div class="list-group">
            @foreach ($notifications as $notification)
                <div class="list-group-item d-flex justify-content-between align-items-start {{ $notification->read_at ? 'text-muted' : 'bg-light fw-bold' }}">
                    <div class="flex-grow-1">
                        {{-- You'll need to customize how each notification's data is displayed --}}
                        @if ($notification->type === 'App\Notifications\AppointmentScheduled')
                            Appointment Scheduled: Patient {{ $notification->data['patient_name'] ?? 'N/A' }} on
                            {{ \Carbon\Carbon::parse($notification->data['appointment_datetime'] ?? '')->format('M d, Y H:i') }}
                            for doctor {{ $notification->data['doctor_name'] ?? 'N/A' }}.
                            @if (isset($notification->data['appointment_id']))
                                <a href="{{ route('doctor.appointments.show', $notification->data['appointment_id']) }}" class="text-decoration-none ms-2">View Appointment</a>
                            @endif
                        @elseif ($notification->type === 'App\Notifications\NewMessage')
                            New Message from Patient {{ $notification->data['sender_name'] ?? 'N/A' }}:
                            "{{ Str::limit($notification->data['message_content'] ?? '', 80) }}"
                            @if (isset($notification->data['conversation_id']))
                                <a href="{{ route('doctor.messages.show', $notification->data['conversation_id']) }}" class="text-decoration-none ms-2">View Message</a>
                            @endif
                        @else
                            {{-- Generic display for other notification types --}}
                            Notification ID: {{ $notification->id }}<br>
                            Type: {{ last(explode('\\', $notification->type)) }}<br>
                            Data: {{ json_encode($notification->data) }}
                        @endif
                        <br>
                        <small class="text-secondary mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                            @unless ($notification->read_at)
                                (Unread)
                            @endunless
                        </small>
                    </div>
                    @unless ($notification->read_at)
                        <form action="{{ route('doctor.notifications.markRead', $notification->id) }}" method="POST" class="ms-3">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">Mark as Read</button>
                        </form>
                    @endunless
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @else
        <p class="text-center text-muted">You have no notifications.</p>
    @endif
</div>
@endsection
