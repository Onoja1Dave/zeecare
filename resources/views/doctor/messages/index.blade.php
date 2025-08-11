{{-- resources/views/doctor/messages/index.blade.php --}}
@extends('layouts.doctor')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Your Conversations</h2>
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
            <h5 class="card-title mb-0">Active Chats</h5>
        </div>
        <div class="card-body">
            @if ($conversations->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach ($conversations as $conversation)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">
                                    {{-- CORRECTED LOGIC HERE --}}
                                    <a href="{{ route('doctor.messages.show', $conversation->id) }}" class="text-decoration-none">
                                        @if ($conversation->patient && $conversation->patient->user)
                                            Conversation with Patient: {{ $conversation->patient->user->name }}
                                        @elseif ($conversation->caregiver)
                                            Conversation with Caregiver: {{ $conversation->caregiver->name }}
                                        @else
                                            Unknown Participant
                                        @endif
                                    </a>
                                </h6>
                                @if ($conversation->messages->isNotEmpty()) {{-- Check if messages relationship is not empty --}}
                                    @php
                                        // Get the actual latest message from the loaded collection
                                        $latestMessage = $conversation->messages->first();
                                    @endphp
                                    <small class="text-muted">
                                        {{ Str::limit($latestMessage->content, 50) }}
                                        - {{ $latestMessage->created_at->diffForHumans() }}
                                    </small>
                                @else
                                    <small class="text-muted">No messages yet.</small>
                                @endif
                            </div>
                            {{-- Add unread message count badge here --}}
                            @php
                                // Count unread messages from the other party
                                $unreadCount = $conversation->messages
                                                            ->where('sender_id', '!=', Auth::id())
                                                            ->whereNull('read_at')
                                                            ->count();
                            @endphp
                            @if ($unreadCount > 0)
                                <span class="badge bg-primary rounded-pill">{{ $unreadCount }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-center text-muted">You have no active conversations yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection