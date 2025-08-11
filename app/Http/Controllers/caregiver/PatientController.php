<?php

namespace App\Http\Controllers\Caregiver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient; 
use Carbon\Carbon;
use App\Models\Prescription;


class PatientController extends Controller
{
    /**
     * Display a listing of patients associated with the caregiver.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $caregiver = Auth::user(); // Get the currently authenticated caregiver (which is a User model instance)

        // Retrieve all patients assigned to this caregiver
        // This uses the 'patientsUnderCare()' relationship you have defined in your User model
        $patients = $caregiver->patientsUnderCare()->get();

        // Pass the fetched patients to the view
        return view('caregiver.patients.index', compact('patients'));
    }

    /**
     * Display the specified patient.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\View\View
     */
public function show(Patient $patient)
{
    $caregiver = Auth::user();

    if ($patient->caregiver_id !== $caregiver->id) {
        abort(403, 'Unauthorized access to this patient record.');
    }

    return view('caregiver.patients.show', compact('patient'));
}



}