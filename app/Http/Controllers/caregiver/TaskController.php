<?php

namespace App\Http\Controllers\Caregiver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task; 
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks for the authenticated caregiver.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $caregiverId = Auth::id();

        $tasks = Task::where('caregiver_id', $caregiverId)
                     ->with('patient') 
                     ->orderBy('due_at') 
                     ->get();

        return view('caregiver.tasks.index', compact('tasks'));
    }
/**
     * Display the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function show(Task $task)
    {
       
        if ($task->patient->user_id !== Auth::id() && $task->caregiver_id !== Auth::id()) {
            abort(403, 'Unauthorized action.'); // Or redirect back with an error
        }

        // Eager load patient if not already done by Route Model Binding
        $task->load('patient');

        return view('caregiver.tasks.show', compact('task'));
    }

/**
     * Mark the specified task as complete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsComplete(Request $request, Task $task)
    {
        // Basic Authorization check:
        // Ensure the task belongs to a patient managed by the logged-in caregiver,
        // or that the task is directly assigned to them.
        if ($task->patient->user_id !== Auth::id() && $task->caregiver_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to complete this task.');
        }

        // Validate notes if provided
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $task->update([
            'is_completed' => true,
            'completed_at' => now(), // Set the completion timestamp
            'notes' => $request->input('notes'), // Save caregiver notes
        ]);

        return redirect()->route('caregiver.tasks.show', $task->id)
                         ->with('success', 'Task marked as complete!');
    }


}