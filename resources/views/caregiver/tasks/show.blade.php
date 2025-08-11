{{-- resources/views/caregiver/tasks/show.blade.php --}}
@extends('layouts.caregiver')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Task Details</h2>
        <a href="{{ route('caregiver.tasks.index') }}" class="btn btn-secondary">Back to Tasks List</a>
    </div>

    <div class="card">
        <div class="card-header">
            Task for {{ $task->patient->name ?? 'N/A' }}
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $task->description }}</h5>
            <p class="card-text"><strong>Due At:</strong> {{ $task->due_at->format('M d, Y H:i A') }}</p>
            <p class="card-text">
                <strong>Status:</strong>
                @if($task->is_completed)
                    <span class="badge bg-success">Completed</span>
                @else
                    <span class="badge bg-warning text-dark">Pending</span>
                @endif
            </p>
            @if($task->completed_at)
                <p class="card-text"><strong>Completed On:</strong> {{ $task->completed_at->format('M d, Y H:i A') }}</p>
            @endif
            @if($task->notes)
                <p class="card-text"><strong>Caregiver Notes:</strong> {{ $task->notes }}</p>
            @endif

            <hr>

            @if(!$task->is_completed) {{-- Only show if task is not yet completed --}}
                <form action="{{ route('caregiver.tasks.complete', $task->id) }}" method="POST">
                    @csrf
                    @method('PATCH') {{-- Use PATCH method for updating partial resource --}}

                    <div class="mb-3">
                        <label for="notes" class="form-label">Caregiver Notes (Optional):</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add notes about task completion"></textarea>
                        @error('notes')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success">Mark as Complete</button>
                </form>
            @else
                <p class="text-success mt-3">This task has already been completed.</p>
            @endif

        </div>
    </div>
</div>
@endsection