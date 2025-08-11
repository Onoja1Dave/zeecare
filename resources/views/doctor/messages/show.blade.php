@extends('layouts.doctor')

@php
    use Illuminate\Support\Str; // Add this line if you get 'Class Str not found' error
@endphp

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- Display the patient's name for this conversation --}}
        <h2>Conversation with {{ $otherParticipant->name ?? 'Unknown Patient' }}</h2>
        <a href="{{ route('doctor.messages.index') }}" class="btn btn-secondary">Back to Conversations</a>
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

    <div class="card mb-4">
        <div class="card-body" style="max-height: 500px; overflow-y: auto;" id="messages-container" > {{-- Scrollable chat area --}}
            <div class="messages-list"  id="chat-messages" >
@forelse ($conversation->messages->sortBy('created_at') as $message)
            <div class="d-flex mb-3 {{ $message->sender_id === Auth::id() ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="card {{ $message->sender_id === Auth::id() ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 75%;">
                            <div class="card-body p-2">
                                <p class="mb-0">{{ $message->content }}</p>
                                <small class="text-end d-block {{ $message->sender_id === Auth::id() ? 'text-white-50' : 'text-muted' }}">
                                    {{ $message->sender->name ?? 'Unknown' }} - {{ \Carbon\Carbon::parse($message->created_at)->format('H:i A, M d') }}
                                    @if ($message->sender_id === Auth::id() && $message->read_at)
                                        <i class="fas fa-check-double ms-1" title="Read"></i> {{-- Assuming you have FontAwesome or similar for icons --}}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted"  id="no-messages-text" >No messages in this conversation yet. Send the first one!</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Message Input Form --}}
    <div class="card">
        <div class="card-body">
            <form action="{{ route('doctor.messages.send', $conversation->id) }}" method="POST" id="message-form"  >
                @csrf
                <div class="input-group">
                    <textarea class="form-control @error('content') is-invalid @enderror" name="content" id="message-input"  rows="2" placeholder="Type your message..." required>{{ old('content') }}</textarea>
                    <button type="submit" class="btn btn-success">Send</button>
                </div>
                @error('content')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </form>
        </div>
    </div>
</div>

@push('scripts') {{-- Using @push('scripts') for better organization if your layout has @stack('scripts') --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const messagesContainer = document.getElementById('messages-container');
        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const messageForm = document.getElementById('message-form');
        const noMessagesText = document.getElementById('no-messages-text');
        const authUserId = {{ Auth::id() }}; // Get the authenticated user's ID

        // Function to scroll to the bottom of the chat
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Function to append a new message to the chat
        function appendMessage(message) {
            if (noMessagesText) {
                noMessagesText.remove(); // Remove "No messages yet" text if it exists
            }

            const isSender = message.sender_id === authUserId;
            const justifyClass = isSender ? 'justify-content-end' : 'justify-content-start';
            const cardBgClass = isSender ? 'bg-primary text-white' : 'bg-light';
            const smallTextColorClass = isSender ? 'text-white-50' : 'text-muted';

            const messageHtml = 
                <div class="d-flex mb-3 ${justifyClass}">
                    <div class="card ${cardBgClass}" style="max-width: 75%;">
                        <div class="card-body p-2">
                            <p class="mb-0">${message.content}</p>
                            <small class="text-end d-block ${smallTextColorClass}">
                                ${message.sender_name} - ${message.created_at}
                                ${isSender && message.read_at ? '<i class="fas fa-check-double ms-1" title="Read"></i>' : ''}
                            </small>
                        </div>
                    </div>
                </div>
            ;
            chatMessages.insertAdjacentHTML('beforeend', messageHtml);
            scrollToBottom();
        }

        // Listen for incoming messages via WebSocket
        window.Echo.private('chat.conversation.{{ $conversation->id }}')
            .listen('.message.sent', (e) => { // Use the broadcastAs() name
                // console.log('Message received via Echo:', e.message); // For debugging
                appendMessage(e.message); // Append the received message
            });

        // Handle form submission via AJAX (for instant display for the sender)
        messageForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const form = event.target;
            const formData = new FormData(form);
            const messageContent = formData.get('content');

            // Optionally, add basic client-side validation
            if (!messageContent.trim()) {
                alert('Message content cannot be empty.');
                return;
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json' // Request JSON response
                },
                body: formData
            })
            .then(response => response.json()) // Assuming your controller returns JSON (e.g., the message data)
            .then(data => {
                if (data.success) {
                    // Append the message for the sender instantly
                    appendMessage(data.message);
                    messageInput.value = ''; // Clear the input
                    } else if (data.error) {
                    alert('Error sending message: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the message.');
            });
        });


        // Scroll to bottom when page loads
        scrollToBottom();
    });
</script>
@endpush



@endsection