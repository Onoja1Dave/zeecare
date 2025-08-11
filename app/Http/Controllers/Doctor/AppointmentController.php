<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient; 
use App\Models\Appointment;
use Carbon\Carbon;


class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments for the doctor.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
     $doctorUser = Auth::user();

        // Optional: Security check (though middleware should handle primary access control)
        // If your User model has an isDoctor() method, you can use it:
        // if (!$doctorUser || !$doctorUser->isDoctor()) {
        //     return redirect()->route('home')->with('error', 'Unauthorized access.');
        // }

        // Fetch appointments where the patient is assigned to the current doctor.
        // We use whereHas to filter Appointments based on their patient's doctor_id.
        $appointments = Appointment::whereHas('patient', function ($query) use ($doctorUser) {
                                // The 'patient' relationship on Appointment model
                                // filters for patients where the 'assigned_doctor_id' matches the current doctor's ID.
                                $query->where('assigned_doctor_id', $doctorUser->id);
                            })
                            // Eager load the patient and the patient's user details for display in the view.
                            ->with('patient.user')
                            // Order them, e.g., by appointment date and time, descending (latest first)
                            ->orderBy('appointment_datetime', 'desc')
                            ->get();


        // You can optionally separate them into upcoming and past appointments for better display
        $upcomingAppointments = $appointments->filter(function($appointment) {
            return Carbon::parse($appointment->appointment_datetime)->isFuture();
        });

        $pastAppointments = $appointments->filter(function($appointment) {
            return Carbon::parse($appointment->appointment_datetime)->isPast();
        });


        // Pass the fetched appointments to the view
        return view('doctor.appointments.index', compact('appointments', 'upcomingAppointments', 'pastAppointments'));
    }

    /**
     * Show the form for creating a new appointment.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Fetch patients assigned to this doctor for the dropdown in the form
        $doctor = Auth::user();
        $patients = $doctor->patientsAssigned()->with('user')->get(); // Using patientsAssigned as per our last update

        return view('doctor.appointments.create', compact('patients')); // You'll need to create this Blade file
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
public function store(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'appointment_datetime' => 'required|date|after_or_equal:now',
        'notes' => 'nullable|string|max:1000',
    ]);

    // Create the new appointment AND ASSIGN IT TO THE $appointment VARIABLE
    $appointment = Appointment::create([ // <-- ADDED '$appointment =' HERE
        'doctor_id' => Auth::id(), // Assign the current doctor
        'patient_id' => $request->patient_id,
        'appointment_datetime' => $request->appointment_datetime,
        'status' => 'scheduled',
        'notes' => $request->notes,
    ]);

    // Now $appointment is defined and has the ID of the newly created appointment
    return redirect()->route('doctor.appointments.show', $appointment->id)->with('success', 'Appointment created successfully!');
}


public function show(Appointment $appointment)
{
    $appointment->load(['patient.user', 'doctor']);

    // Ensure this Blade file exists!
    return view('doctor.appointments.show', compact('appointment'));
}


public function updateStatus(Request $request, Appointment $appointment)
{
    if ($appointment->doctor_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }
    $request->validate([
        'status' => 'required|in:completed,missed,cancelled',
    ]);
    $appointment->status = $request->status;
    $appointment->save();
    return back()->with('success', 'Appointment status updated to ' . $request->status . '.');
}


}