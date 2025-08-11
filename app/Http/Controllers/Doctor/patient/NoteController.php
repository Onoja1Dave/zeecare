<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DoctorNote;
use App\Notifications\NewDoctorNote;

class NoteController extends Controller
{
    /**
     * Display a listing of notes for the patient.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch notes relevant to the authenticated patient here
        // Example: If your Patient model has a 'notes' relationship through the User model,
        // you might fetch them via:
        // $patientUser = Auth::user();
        // $notes = $patientUser->patient->doctorNotes()->latest()->get(); // Assuming 'patient' and 'doctorNotes' relationships exist
        // return view('patient.notes.index', compact('notes'));

        return view('patient.notes.index'); // Placeholder for now, but this will need actual note fetching
    }
/**
     * Display the specified doctor note.
     *
     * @param  \App\Models\DoctorNote  $doctorNote
     * @return \Illuminate\View\View
     */
   
     public function show(DoctorNote $doctorNote)
    {
        $patientUser = Auth::user();

        if ($doctorNote->patient->user->id !== $patientUser->id) {
             abort(403, 'Unauthorized access to this note.');
        }

        $notification = $patientUser->notifications()
                                    ->where('type', NewDoctorNote::class) // This line needs NewDoctorNote to be defined via a 'use' statement
                                    ->whereJsonContains('data->note_id', $doctorNote->id)
                                    ->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            // You can re-add the dd() here temporarily if you want to verify it's hit:
            // dd('Notification marked as read for ID: ' . $notification->id);
        }

        return view('patient.notes.show', compact('doctorNote'));
    }
}