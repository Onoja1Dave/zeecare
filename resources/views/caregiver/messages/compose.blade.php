{{-- resources/views/caregiver/messages/compose.blade.php --}}
@extends('layouts.caregiver')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Message a Doctor</h2>
        <a href="{{ route('caregiver.messages.index') }}" class="btn btn-secondary">Back to My Conversations</a>
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

    @if($doctors->isEmpty())
        <div class="alert alert-info" role="alert">
            No doctors found associated with your assigned patients whom you can message.
        </div>
    @else
        <p>Select a doctor associated with your patients to start a new conversation or continue an existing one:</p>
        <div class="list-group">
            @foreach($doctors as $doctor)
                {{-- Form to find or create conversation and redirect --}}
                <form action="{{ route('caregiver.messages.findOrCreateConversation') }}" method="POST" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                    <span>{{ $doctor->name }} <span class="badge bg-primary">Doctor</span></span>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Message</button>
                </form>
            @endforeach
        </div>
    @endif
</div>
@endsection