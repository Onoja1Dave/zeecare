<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prescription;
use App\Models\Patient; 
use App\Models\MedicationDose;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;




class PrescriptionController extends Controller
{
    /**
     * Display a listing of prescriptions created by the doctor.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $doctorUser = Auth::user();

        // Optional: Security check (though middleware should handle primary access control)
        // if (!$doctorUser || !$doctorUser->isDoctor()) { // Assuming you have an isDoctor() method on User
        //     return redirect()->route('home')->with('error', 'Unauthorized access.');
        // }

        // Fetch prescriptions where the 'doctor_id' matches the authenticated doctor's ID.
        // Eager load the 'patient' relationship and then the 'user' relationship on the patient
        // to get patient names for the view.
        $prescriptions = Prescription::where('doctor_id', $doctorUser->id)
                                    ->with('patient.user') // Eager load patient and patient's user details
                                    ->orderBy('created_at', 'desc') // Order by latest prescribed first
                                    ->get();

        // Pass the fetched prescriptions to the view
        return view('doctor.prescriptions.index', compact('prescriptions'));
    }

/**
     * Show the form for creating a new prescription for a specific patient.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\View\View
     */
    public function create(Patient $patient)
    {
        // Optional: Add authorization check to ensure the doctor can prescribe for this patient
        // For example, if the patient must be assigned to this doctor:
        // if ($patient->assigned_doctor_id !== Auth::id()) {
        //     abort(403, 'Unauthorized to create prescriptions for this patient.');
        // }

        return view('doctor.prescriptions.create', compact('patient'));
    }

    // You will also need a 'store' method to handle the form submission and save the prescription
    // public function store(Request $request)
    // {
    //     // ... logic to validate and save the prescription ...
    // }

public function store(Request $request)
    {
        $doctorUser = Auth::user();

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'drug_name' => 'required|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'frequency' => 'required|string|max:255',
            'duration' => 'required|integer|min:1|max:365',
        ]);

        $prescription = Prescription::create([ // Ensure you use 'Prescription' here, not 'prescription' lowercase
            'patient_id' => $request->patient_id,
            'doctor_id' => $doctorUser->id,
            'drug_name' => $request->drug_name,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
        ]);

        // 3. Generate Medication Doses based on frequency and duration
        $startDate = Carbon::now()->startOfDay(); // Start generating doses from today, beginning of day
        $durationInDays = (int) $request->duration;
        $dosesToCreate = [];

        // Determine doses per day and initial times based on frequency
        $frequencyLower = strtolower($request->frequency);
        $dosesPerDay = 0;
        $initialTimes = []; // Array of Carbon times for first day's doses

        switch ($frequencyLower) {
            case 'once a day':
            case 'once daily':
                $dosesPerDay = 1;
                $initialTimes = [Carbon::parse('09:00')]; // e.g., 9 AM
                break;
            case 'twice a day':
            case 'twice daily':
                $dosesPerDay = 2;
                $initialTimes = [Carbon::parse('09:00'), Carbon::parse('21:00')]; // e.g., 9 AM, 9 PM
                break;
            case 'thrice a day':
            case 'thrice daily':
                $dosesPerDay = 3;
                // Note: 1 AM for 'thrice daily' might mean a dose for the next calendar day's early morning.
                // We'll address if generated doses are in the past later, but for now, let's see them generated.
                $initialTimes = [Carbon::parse('09:00'), Carbon::parse('17:00'), Carbon::parse('01:00')]; // e.g., 9 AM, 5 PM, 1 AM (next day)
                break;
            case 'four times a day':
            case 
            'four times daily':
                $dosesPerDay = 4;
                // Note: 00:00 (midnight) for 'four times daily' might also be for next calendar day's early morning.
                $initialTimes = [Carbon::parse('06:00'), Carbon::parse('12:00'), Carbon::parse('18:00'), Carbon::parse('00:00')]; // e.g., Every 6 hours
                break;
            case 'every 8 hours':
                $dosesPerDay = 3;
                $initialTimes = [Carbon::parse('08:00'), Carbon::parse('16:00'), Carbon::parse('00:00')]; // e.g., 8 AM, 4 PM, 12 AM (next day)
                break;
            case 'every 6 hours':
                $dosesPerDay = 4;
                $initialTimes = [Carbon::parse('06:00'), Carbon::parse('12:00'), Carbon::parse('18:00'), Carbon::parse('00:00')]; // e.g., Every 6 hours
                break;
            // Add more cases for other frequencies you expect (e.g., 'as needed', 'every other day')
            default:
                // Default to once a day if frequency is not recognized
                $dosesPerDay = 1;
                $initialTimes = [Carbon::parse('09:00')];
                break;
        }

        for ($day = 0; $day < $durationInDays; $day++) {
            $currentDay = $startDate->copy()->addDays($day);

            foreach ($initialTimes as $initialTime) {
                // Combine current day with initial time to get scheduled_at
                $scheduledAt = $currentDay->copy()->setTime($initialTime->hour, $initialTime->minute, $initialTime->second);
$dosesToCreate[] = [
                    'prescription_id' => $prescription->id,
                    'patient_id' => $request->patient_id,
                    'scheduled_at' => $scheduledAt,
                    'status' => 'scheduled', 
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

     

        // 4. Redirect back to the patient's profile with a success message
        return redirect()->route('doctor.patients.show', $request->patient_id)
                         ->with('success', 'Prescription created successfully!');
    }
}