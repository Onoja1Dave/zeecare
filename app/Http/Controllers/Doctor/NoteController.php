<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\DoctorNote; // <-- ENSURE this is the correct model name for your notes (it should be DoctorNote based on your NewDoctorNote class)
use App\Models\User; // Make sure User model is imported if not already
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewDoctorNote;

class NoteController extends Controller
{
    public function index()
    {
        // Example: Fetch notes for the authenticated doctor's patients
        $doctorPatients = Auth::user()->patients; // Assuming this relationship exists
        $notes = Note::whereIn('patient_id', $doctorPatients->pluck('id'))->latest()->get();

        return view('doctor.notes.index', compact('notes'));
    }

    /**
     * Show the form for creating a new note for a specific patient.
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\View\View
     */
    public function create(Patient $patient)
    {
        // You might want to add a check here to ensure the doctor is assigned to this patient
        // or has permission to create notes for them.
        // For example: if ($patient->assigned_doctor_id !== Auth::id()) { abort(403); }

        return view('doctor.notes.create', compact('patient'));
    }

 /**
     * Store a newly created note in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'content' => 'required|string',
        ]);

        // First, retrieve the Patient model. We need it for the notification.
        $patient = Patient::find($request->patient_id);
        if (!$patient) {
            // This should ideally be caught by validation, but it's a good safeguard
            return redirect()->back()->with('error', 'Patient not found.');
        }

        // Create the doctor note
        // Ensure you are consistently using 'DoctorNote' as your model name here.
        $doctorNote = DoctorNote::create([ // <-- CHANGED: Using $doctorNote variable and DoctorNote model
            'patient_id' => $patient->id, // Use $patient->id for clarity
            'doctor_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Dispatch the notification to the patient's associated user
        // The NewDoctorNote constructor requires both the DoctorNote and Patient objects.
        if ($patient->user) { // Ensure the patient has a linked user
            $patient->user->notify(new NewDoctorNote($doctorNote, $patient)); // <-- CHANGED: Pass both $doctorNote and $patient
        }

        return redirect()->route('doctor.patients.show', $patient->id) // Use $patient->id for consistency
                         ->with('success', 'Note added successfully!');
    }


}