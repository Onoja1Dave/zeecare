<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient; 
use App\Models\User;

class PatientController extends Controller
{
    /**
     * Display a listing of patients associated with the doctor.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
$doctorUser = Auth::user();

        // Optional: Security check to ensure only doctors access this
        // This should ideally be handled by middleware, but a check here adds safety.
        // Assuming your User model has an isDoctor() method
        if (!$doctorUser || !$doctorUser->isDoctor()) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        // Fetch patients related to the authenticated doctor
        // We eager load the 'user' relationship on Patient to get patient's user details
        $patients = $doctorUser->patients()->with('user')->get();

        return view('doctor.patients.index', compact('patients'));
    }

    public function show(Patient $patient)
    {
        // At this point, $patient contains the Patient model found by the ID in the URL.
        // Load additional relationships needed for the patient's profile view
        $patient->load([
            'user', // To get the Patient's User details (if the Patient has a user account)
            'appointments' => function ($query) {
                $query->orderBy('appointment_datetime', 'desc')->take(5); // Load recent appointments
            },
            'prescriptions' => function ($query) {
                $query->latest()->take(5); // Load recent prescriptions
            },
            'tasks' => function ($query) {
                $query->where('is_completed', false)->orderBy('due_at')->take(5); // Load pending tasks
            },
            'alerts' => function ($query) {
                $query->where('is_resolved', false)->latest()->take(5); // Load unresolved alerts
            },
            'assignedDoctor', // <-- CORRECTED THIS LINE: Removed '.user'
        ]);

        return view('doctor.patients.show', compact('patient'));
    }


public function showAssignForm()
   
{
        // Get the currently authenticated doctor's ID
        $doctor_id = Auth::id();

        // Fetch patients who are not currently assigned to any doctor,
        // or specifically not assigned to *this* doctor if you allow re-assignment.
        $unassignedPatients = Patient::whereNull('assigned_doctor_id')
                                   ->with('user') // Assuming Patient has a 'user' relationship to get name/email
                                   ->get();

        return view('doctor.patients.assign-form', compact('unassignedPatients'));
    }

    public function assignPatient(Request $request)
    {
        // Validate the request
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
        ]);

        // Get the authenticated doctor
        $doctor = Auth::user();

        // Find the patient and assign the doctor_id
        $patient = Patient::find($request->input('patient_id'));

        // Check if patient exists AND is not already assigned to ANY doctor
        // If you want to allow re-assignment, remove the is_null($patient->doctor_id) check
        if ($patient && is_null($patient->assigned_doctor_id)) {
            $patient->assigned_doctor_id = $doctor->id; // Assign the doctor's ID
            $patient->save();
            return redirect()->route('doctor.patients.index')->with('success', 'Patient assigned successfully!');
        }

        return redirect()->back()->with('error', 'Patient could not be assigned or is already assigned.');
    }


}