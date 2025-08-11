{{-- resources/views/doctor/messages/create.blade.php --}}

@extends('layouts.doctor')

@section('content')
<div class="container">
    <h2>Start New Conversation</h2>

    <form action="{{ route('doctor.messages.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="patient_id" class="form-label">Patient</label>
            <select class="form-control" id="patient_id" name="patient_id" required>
                @if($patient)
                    <option value="{{ $patient->id }}" selected>{{ $patient->user->name ?? $patient->name }}</option>
                @else
                    <option value="">Select a Patient</option>
                    {{-- You'll need to pass a list of patients from the controller if not pre-selected --}}
                    {{-- @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->user->name ?? $p->name }}</option>
                    @endforeach --}}
                @endif
            </select>
            @error('patient_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Message</label>
            <textarea class="form-control" id="body" name="body" rows="5" required>{{ old('body') }}</textarea>
            @error('body')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>
@endsection