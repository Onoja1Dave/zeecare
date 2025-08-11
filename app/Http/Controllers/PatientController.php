<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; // <-- ADD THIS LINE
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\DoctorNote;
use App\Events\MessageSent;



class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $patients = Patient::all();
        return response()->json($patients);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'contact_number' => 'nullable|max:20',
        ]);

        // Create a new Patient model instance
        $patient = new Patient;
        $patient->name = $validatedData['name'];
        $patient->date_of_birth = $validatedData['date_of_birth'] ?? null;
        $patient->gender = $validatedData['gender'] ?? null;
        $patient->contact_number = $validatedData['contact_number'] ?? null;
        $patient->save();

        // Redirect to the patients index page with a success message
        return redirect()->route('patients.index')->with('success', 'Patient created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

   

    /**
   

    
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

public function __construct()
    {
        //$this->middleware('auth');
    }

    // Other methods you might have

     public function edit()
    {
        $user = request()->user();
        $patient = null;
        if ($user) {
            $patient = $user->patientInfo;
        }
    
        return view('patient.edit', compact('patient'));
    }

    public function update(Request $request)
    {
        // Logic to update the patient's profile will go here
    }

public function updateProfile(Request $request)
{
    $user = $request->user();
    $patient = $user->patientInfo;

    if ($patient) {
        $validatedData = $request->validate([
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'contact_number' => 'nullable|max:20',
        ]);

        $patient->update($validatedData);

        return redirect()->route('home')->with('success', 'Profile updated successfully!');
    }

    return redirect()->route('home')->with('error', 'Unable to update profile.');
}

public function viewAppointments()
{
    $user = request()->user();
    $patient = $user->patientInfo;

    $appointments = $patient->appointments()->with('doctor')->orderBy('appointment_datetime')->get();

    return view('patients.appointments.index', compact('appointments'));
}

public function viewPrescriptions()
{
    $user = request()->user();
    $patient = $user->patientInfo;

    $prescriptions = $patient->prescriptions()->with('doctor')->get();

    return view('patients.prescriptions.index', compact('prescriptions'));
}
public function viewNotes()
{
    $user = request()->user();
    $patient = $user->patientInfo;

    $notes = $patient->notes()->with('doctor')->orderBy('created_at', 'desc')->get();

    return view('patients.notes.index', compact('notes'));
}

/**
     * Display a specific doctor's note for the authenticated patient.
     * Used for the notification link.
     */
    public function showNote(DoctorNote $doctorNote)
    {
        // Security check: Ensure the note belongs to the authenticated patient
        // We need to ensure the patient model has a 'user' relationship to link to Auth::user()
        if ($doctorNote->patient->user->id !== Auth::id()) { // Adjust if your Patient model doesn't have a 'user' relationship direct to Auth::user()
            abort(403, 'Unauthorized action.'); // Or redirect with an error message
        }

        return view('patients.notes.show', compact('doctorNote'));
    }



public function markMedicationAsTaken(Request $request, \App\Models\MedicationDose $medicationDose)
{
    // Ensure the patient marking the medication is the owner of the dose
    if ($medicationDose->patient_id !== $request->user()->patientInfo->id) {
        return back()->with('error', 'You are not authorized to mark this medication as taken.');
    }

    $medicationDose->update(['taken_at' => now(), 'status' => 'taken']);

    return back()->with('success', 'Medication marked as taken.');
}



}
