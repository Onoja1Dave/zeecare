{{-- resources/views/caregiver/tasks/index.blade.php --}}
@extends('layouts.caregiver') {{-- Using 'layouts.caregiver' as per your layout setup --}}

@section('content')
<div class="container mt-4">
    <h2>My Assigned Tasks</h2>

    @if($tasks->isEmpty())
        <p>You have no tasks assigned to you at the moment.</p>
    @else
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Description</th>
                    <th>Due At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->patient->name ?? 'N/A' }}</td> {{-- Display patient name --}}
                        <td>{{ $task->description }}</td>
                        <td>{{ $task->due_at->format('M d, Y H:i A') }}</td> {{-- Format datetime --}}
                        <td>
                            @if($task->is_completed)
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td>
                         <a href="{{ route('caregiver.tasks.show', $task->id) }}" class="btn btn-sm btn-outline-primary">View/Manage</a>   
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection