{{-- resources/views/doctor/notes/create.blade.php --}}

@extends('layouts.doctor') {{-- Assuming you have a layout for doctors --}}

@section('content')
<div class="container">
    <h2>Add New Note for {{ $patient->user->name ?? $patient->name }}</h2>

    <form action="{{ route('doctor.notes.store') }}" method="POST">
        @csrf

        {{-- Hidden input for patient_id --}}
        <input type="hidden" name="patient_id" value="{{ $patient->id }}">

        <div class="mb-3">
            <label for="content" class="form-label">Note Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
            @error('content')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Add other form fields here if your Note model has more columns --}}

        <button type="submit" class="btn btn-primary">Save Note</button>
        <a href="{{ route('doctor.patients.show', $patient->id) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection