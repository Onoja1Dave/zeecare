{{-- resources/views/caregiver/messages/show.blade.php --}}
@extends('layouts.caregiver')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Conversation with Dr. {{ $doctor->name ?? 'N/A Doctor' }}</h2>
        <a href="{{ route('caregiver.messages.index') }}" class="btn btn-secondary">Back to Conversations</a>
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

    <div class="card mb-4" style="height: 500px; overflow-y: auto;">
        <div class="card-body d-flex flex-column-reverse">
            {{-- Messages will be displayed here --}}
            @forelse($conversation->messages->reverse() as $message) {{-- Reverse to show newest at bottom --}}
                <div class="message-bubble @if($message->sender_id === Auth::id()) sent @else received @endif">
                    <div class="message-content">
                        <strong>{{ $message->sender->name ?? 'N/A' }}:</strong> {{ $message->content }}
                    </div>
                    <small class="message-time">{{ $message->created_at->format('M d, H:i A') }}</small>
                    @if($message->sender_id === Auth::id() && $message->read_at)
                        <small class="text-success ms-2"><i class="bi bi-check-all"></i> Read</small>
                    @endif
                </div>
            @empty
                <p class="text-center text-muted">No messages yet. Start the conversation!</p>
            @endforelse
        </div>
    </div>

    {{-- Message Input Form --}}
    <div class="card">
        <div class="card-body">
            <form action="{{ route('caregiver.messages.send', $conversation->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="content" class="form-label visually-hidden">Your Message</label>
                    <textarea class="form-control" id="content" name="content" rows="3" placeholder="Type your message here..." required></textarea>
                    @error('content')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Message</button>
            </form>
        </div>
    </div>
</div>

<style>
    .message-bubble {
        padding: 8px 12px;
        border-radius: 15px;
        margin-bottom: 10px;
        max-width: 70%;
        word-wrap: break-word;
        font-size: 0.9em;
    }
    .message-bubble.sent {
        background-color: #dcf8c6; /* Light green for sent messages */
        align-self: flex-end;
        margin-left: auto;
    }
    .message-bubble.received {
        background-color: #e0e0e0; /* Light gray for received messages */
        align-self: flex-start;
        margin-right: auto;
    }
    .message-time {
        font-size: 0.7em;
        color: #888;
        display: block;
        text-align: right; /* For sent messages */
        margin-top: 3px;
    }
    .message-bubble.received .message-time {
        text-align: left; /* For received messages */
    }
</style>
@endsection