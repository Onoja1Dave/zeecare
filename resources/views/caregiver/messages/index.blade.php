{{-- resources/views/caregiver/messages/index.blade.php --}}
@extends('layouts.caregiver')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Conversations with Doctors</h2>
        <a href="{{ route('caregiver.messages.compose') }}" class="btn btn-primary">Start New Conversation</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($latestMessages->isEmpty())
        <p>You have no active conversations with doctors.</p>
    @else
        <div class="list-group">
            @foreach($latestMessages as $data)
                @php
                    $conversation = $data['conversation'];
                    $lastMessage = $data['last_message'];
                    $unreadCount = $data['unread_count'];
                @endphp
                <a href="{{ route('caregiver.messages.show', $conversation->id) }}" class="list-group-item list-group-item-action @if($unreadCount > 0) list-group-item-info @endif">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">
                            Conversation with Dr. {{ $conversation->doctor->name ?? 'N/A Doctor' }}
                        </h5>
                        <small class="text-muted">
                            @if($lastMessage)
                                {{ $lastMessage->created_at->diffForHumans() }}
                            @else
                                No messages yet
                            @endif
                        </small>
                    </div>
                    <p class="mb-1 text-truncate">
                        @if($lastMessage)
                            {{ $lastMessage->sender_id === Auth::id() ? 'You: ' : ($lastMessage->sender->name ?? 'N/A') . ': ' }}
                            {{ $lastMessage->content }}
                        @else
                            Start the conversation!
                        @endif
                    </p>
                    @if($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $unreadCount }} Unread</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection