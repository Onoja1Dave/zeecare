@extends('layouts.patient') 
@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp

@section('content')
<div class="container-fluid py-4"> {{-- Changed to container-fluid for wider layout --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- Display doctor's name (corrected access) --}}
        <h2>Conversation with Dr. {{ $conversation->doctor->name ?? 'Unknown Doctor' }}</h2>
        {{-- Corrected route name --}}
        <a href="{{ route('patient.messages.index') }}" class="btn btn-secondary">Back to Conversations</a>
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

    <div class="card mb-4 shadow-sm animate-card">
        {{-- Added id="messages-container" here for scrolling --}}
        <div class="card-body" style="max-height: 60vh; overflow-y: auto;" id="messages-container"> {{-- Increased max-height --}}
            {{-- Added id="chat-messages" here for appending new messages --}}
            <div class="messages-list" id="chat-messages">
                {{-- CORRECTED LOOP: Iterate over $conversation->messages --}}
                @forelse ($conversation->messages as $message)
                    {{-- Check if the sender is the authenticated patient --}}
                    <div class="d-flex mb-3 {{ $message->sender_id === Auth::id() ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="message-bubble p-3 rounded {{ $message->sender_id === Auth::id() ? 'bg-primary-filled text-white' : 'bg-light text-dark border' }}" style="max-width: 75%;">
                            <div class="fw-bold mb-1">
                                {{ $message->sender_id === Auth::id() ? 'You' : ($message->sender->name ?? 'Unknown') }}
                            </div>
                            <p class="mb-0">{{ $message->content }}</p>
                            <small class="text-end d-block {{ $message->sender_id === Auth::id() ? 'text-white-50' : 'text-muted' }}">
                                {{ Carbon::parse($message->created_at)->format('H:i A, M d') }}
                                @if ($message->sender_id === Auth::id() && $message->read_at)
                                    <i class="fas fa-check-double ms-1" title="Read"></i>
                                @elseif ($message->sender_id === Auth::id())
                                     <i class="fas fa-check ms-1" title="Sent"></i>
                                @endif
                            </small>
                        </div>
                    </div>
                @empty
                    {{-- Added id="no-messages-text" to remove it dynamically --}}
                    <p class="text-center text-muted" id="no-messages-text">No messages in this conversation yet. Send the first one!</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Message Input Form --}}
    <div class="card shadow-sm animate-card">
        <div class="card-body">
            {{-- Corrected route name --}}
            <form action="{{ route('patient.messages.send', $conversation->id) }}" method="POST" id="message-form">
                @csrf
                <div class="input-group">
                    {{-- Added id="message-input" to clear input after sending --}}
                    <textarea class="form-control @error('content') is-invalid @enderror" name="content" id="message-input" rows="2" placeholder="Type your message..." required>{{ old('content') }}</textarea>
                    <button type="submit" class="btn btn-primary-filled">Send</button> {{-- Changed to primary-filled for consistency --}}
                    </div>
                @error('content')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </form>
        </div>
    </div>
</div>
{{-- This script block uses @push('scripts') to include it in your layout's @stack('scripts') --}}
@push('scripts')
<script>
 document.addEventListener('DOMContentLoaded', function () {
        const messagesContainer = document.getElementById('messages-container');
        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const messageForm = document.getElementById('message-form');
        let noMessagesText = document.getElementById('no-messages-text'); // Use 'let' here for reassigning to null
        const authUserId = {{ Auth::id() }};

        // Initial check for elements
        console.log('DOM Loaded. messagesContainer:', messagesContainer);
        console.log('DOM Loaded. chatMessages:', chatMessages);

        function scrollToBottom() {
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                console.log('Scrolled to bottom.');
            } else {
                console.log('messagesContainer not found for scrolling.');
            }
        }

        function appendMessage(message) {
            console.log('Attempting to append message:', message);

            if (!message  !message.content  !message.sender_id) {
                console.error('Invalid message object received by appendMessage:', message);
                return;
            }

            if (noMessagesText) {
                console.log('Removing "no messages" text.');
                noMessagesText.remove();
                noMessagesText = null; // Set to null so it's not removed again
            }

            const isSender = message.sender_id === authUserId;
            const justifyClass = isSender ? 'justify-content-end' : 'justify-content-start';
            const cardBgClass = isSender ? 'bg-primary-filled text-white' : 'bg-light text-dark border';
            const smallTextColorClass = isSender ? 'text-white-50' : 'text-muted';

            const messageHtml = 
                <div class="d-flex mb-3 ${justifyClass}">
                    <div class="message-bubble p-3 rounded ${cardBgClass}" style="max-width: 75%;">
                        <div class="fw-bold mb-1">
                            ${isSender ? 'You' : (message.sender_name || 'Unknown')}
                        </div>
                        <p class="mb-0">${message.content}</p>
                        <small class="text-end d-block ${smallTextColorClass}">
                            ${message.created_at_formatted || message.created_at}
                            ${isSender && message.read_at ? '<i class="fas fa-check-double ms-1" title="Read"></i>' : (isSender ? '<i class="fas fa-check ms-1" title="Sent"></i>' : '')}
                        </small>
                    </div>
                </div>
            ;

            if (chatMessages) {
                chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                console.log('Message HTML appended to chatMessages.');
                scrollToBottom();
            } else {
                console.log('chatMessages element not found for appending.');
            }
        }

        // Listen for incoming messages via WebSocket using Laravel Echo
        window.Echo.private('chat.conversation.{{ $conversation->id }}')
            .listen('.MessageSent', (e) => {
                console.log('Message received via Echo (Patient):', e.message);
                if (e.message.sender_id !== authUserId) {
                    appendMessage(e.message);
                } else {
                    console.log('Received own message via Echo, skipping append to avoid duplication.');
                }
            });

        // Handle form submission via AJAX for instant feedback to the sender
        messageForm.addEventListener('submit', function(event) {
            event.preventDefault();
            console.log('Form submission intercepted.');

            const form = event.target;
            const formData = new FormData(form);
            const messageContent = formData.get('content');
            if (!messageContent.trim()) {
                alert('Message content cannot be empty.');
                return;
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                console.log('Fetch response received:', response);
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                console.log('Parsed JSON data:', data);
                if (data.success && data.message) {
                    console.log('Success received. Appending message:', data.message);
                    appendMessage(data.message);
                    messageInput.value = '';
                    console.log('Message input cleared.');
                } else if (data.error) {
                    alert('Error sending message: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                if (error.errors && error.errors.content) {
                    alert('Validation Error: ' + error.errors.content.join(', '));
                } else {
                    alert('An error occurred while sending the message.');
                }
            });
        });

        scrollToBottom();
    });
</script>
@endpush