@extends('layouts.patient')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Your Conversations') }}</div>

                <div class="card-body">
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

                    @if ($conversations->isEmpty())
                        <p>You don't have any active conversations yet.</p>
                        {{-- Optional: A button to start a new conversation if you have a doctor list --}}
                        {{-- <a href="#" class="btn btn-primary">Start New Conversation</a> --}}
                    @else
                        <ul class="list-group">
                            @foreach ($conversations as $conversation)
                                <a href="{{ route('patient.messages.show', $conversation->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Dr. {{ $conversation->doctor->name ?? 'Unknown Doctor' }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            @if ($conversation->messages->isNotEmpty())
                                                {{ Str::limit($conversation->messages->first()->content, 50) }}
                                                <span class="ms-2"> - {{ \Carbon\Carbon::parse($conversation->messages->first()->created_at)->diffForHumans() }}</span>
                                            @else
                                                No messages yet.
                                            @endif
                                        </small>
                                    </div>
                                    {{-- Optional: Badge for unread messages --}}
                                    {{-- <span class="badge bg-primary rounded-pill">3</span> --}}
                                </a>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection