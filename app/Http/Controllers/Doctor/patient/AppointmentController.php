<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $patient = Auth::user();
        $patientProfile = $patient->patientProfile;

        if (!$patientProfile || !$patientProfile->assigned_doctor_id) {
            $appointments = collect();
        } else {
            // Get the query builder instance
            $query = $patient->patientAppointments();

            
            $appointments = $query->latest('appointment_datetime')->paginate(10);
        }

        // This line will not be reached due to dd() above for now
        return view('patient.appointments.index', compact('appointments'));
    }
}