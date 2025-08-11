<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prescription;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of prescriptions for the patient.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
      $patient = Auth::user();
        // Fetch prescriptions for the authenticated patient
        // This assumes a relationship like $patient->prescriptions()
        $prescriptions = $patient->prescriptions()->latest()->paginate(10); // Or by prescription date

        return view('patient.prescriptions.index', compact('prescriptions'));  
    }
}