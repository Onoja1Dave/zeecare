@extends('layouts.patient') {{-- Make sure this extends your main patient layout (e.g., layouts.app, layouts.dashboard, etc.) --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10"> {{-- Increased column size for better display --}}
            <div class="card mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('All Notifications') }}</h4>
                    @if ($notifications->isNotEmpty())
                        {{-- Optional: Button to mark all as read --}}
                        <button id="mark-all-read-btn" class="btn btn-sm btn-outline-success">Mark All as Read</button>
                    @endif
                </div>

                <div class="card-body">
                    @if ($notifications->isEmpty() && request()->get('page') <= 1)
                        <p class="text-center text-muted py-4">You have no notifications at the moment.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($notifications as $notification)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 {{ is_null($notification->read_at) ? 'unread-notification-item' : '' }}">
                                    <div>
                                        @php
                                            $notificationData = $notification->data;
                                            $iconClass = 'fas fa-info-circle text-info'; // Default icon
                                            $message = 'New Notification'; // Default message

                                            // Determine icon and message based on notification type
                                            if (isset($notificationData['type'])) {
                                                if ($notificationData['type'] == 'doctor_note_added') {
                                                    $iconClass = 'fas fa-notes-medical text-primary';
                                                    $doctorName = $notificationData['doctor_name'] ?? 'Unknown';
                                                    $message = "A new note has been added by Dr. {$doctorName} for you.";
                                                    $snippet = Str::limit($notificationData['note_content_snippet'] ?? 'No snippet available.', 70);
                                                } elseif ($notificationData['type'] == 'appointment_scheduled') {
                                                    $iconClass = 'fas fa-calendar-check text-success';
                                                    $message = $notificationData['message'] ?? 'New appointment scheduled.';
                                                }
                                                // Add more conditions for other notification types here
                                            } elseif (isset($notificationData['message'])) {
                                                $message = $notificationData['message']; // Generic message if 'type' is not set
                                            }
                                        @endphp
                                        <i class="{{ $iconClass }} me-2"></i>
                                        <strong>{{ $message }}</strong>
                                        @if(isset($snippet))
                                            <br>
                                            <small class="text-muted ms-4 fw-light">"{{ $snippet }}"</small>
                                        @endif
                                        <br>
                                        <small class="text-muted ms-4 fw-light">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                    </div>
                                    <div>
                                        @if (is_null($notification->read_at))
                                            {{-- Conditional actions based on type, similar to dashboard card --}}
                                            @if (isset($notificationData['type']) && $notificationData['type'] == 'doctor_note_added' && isset($notificationData['link']))
                                                <a href="{{ $notificationData['link'] }}" class="btn btn-sm btn-info shadow-sm">View Note</a>
                                            @else
                                                {{-- Generic 'Mark as Read' for other types if no specific view link --}}
                                                <button class="btn btn-sm btn-outline-secondary mark-notification-read shadow-sm" data-id="{{ $notification->id }}">Mark as Read</button>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary opacity-75 py-2 px-3">Read</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }} {{-- Laravel pagination links --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Optional: JavaScript for Mark All as Read button --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const markAllReadBtn = document.getElementById('mark-all-read-btn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function () {
                fetch('{{ route('patient.notifications.mark-all-as-read') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message); // Or use a more sophisticated toast notification
                        location.reload(); // Reload the page to show updated status
                    } else {
                        alert('Failed to mark all as read.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
            });
        }

        // Optional: For individual 'Mark as Read' buttons if you want them on this page
        document.querySelectorAll('.mark-notification-read').forEach(button => {
            button.addEventListener('click', function () {
                const notificationId = this.dataset.id;
                fetch(/patient/notifications/${notificationId}/mark-read, { // Ensure this route is correct
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optimistically update the UI or reload
                        location.reload();
                    } else {
                        alert('Failed to mark notification as read.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
            });
        });
    });
</script>
@endsection
