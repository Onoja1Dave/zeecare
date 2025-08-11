<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Patient; // Make sure this is Patient model, not PatientInfo if that's a separate model name
use App\Models\MedicationDose;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\DoctorNote;
use App\Models\PatientMeasurement;
use Carbon\Carbon; // Correct import

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        // Assuming your User model has a relationship named 'patientInfo' that returns a Patient model instance
        $patient = $user->patientProfile;

        if (!$patient) {
            // Handle case where logged-in user is not a patient
            // This return view is temporary if a patient is expected on this dashboard.
            // You might want to redirect to a setup page or a different view.
            return view('Patient.dashboard', ['user' => $user, 'message' => 'Your patient profile is not set up.']);
        }

        // --- Today's Medications ---
        $today = Carbon::today();
        $todayMedications = MedicationDose::where('patient_id', $patient->id)
                                          ->whereDate('scheduled_at', $today)
                                          ->with('prescription') // Make sure to load the medication details
                                          ->get();

        // --- Next Appointment ---
$nextAppointment = $user->patientAppointments()
        ->where('appointment_datetime', '>=', Carbon::now())
                                    ->orderBy('appointment_datetime', 'asc')
                                    ->with('doctor') // Eager load the doctor (User) relationship
                                    ->first();

       // --- All Upcoming Appointments (NEW) ---
        // USE THE patientAppointments() RELATIONSHIP FOR CONSISTENCY AND CORRECT FILTERING
        $allAppointments = $user->patientAppointments() // Use the relationship
                                ->where('appointment_datetime', '>=', Carbon::now())
                                ->orderBy('appointment_datetime', 'asc')
                                ->with('doctor')
                                ->get();

        // --- Prescription Progress Bar ---
        $progressBarText = null;
        $latestPrescription = Prescription::where('patient_id', $patient->id)
                                            ->orderBy('created_at', 'desc')
                                            ->first();

        if ($latestPrescription && $latestPrescription->duration_days) {
            $startDate = Carbon::parse($latestPrescription->created_at);
            $endDate = $startDate->copy()->addDays($latestPrescription->duration_days);
            $today = Carbon::now();

            if ($today->between($startDate, $endDate)) {
                $daysPassed = $startDate->diffInDays($today);
                $totalDays = $latestPrescription->duration_days;
                $progressBarText = "Day " . ($daysPassed + 1) . " of " . $totalDays;
            } elseif ($today->gt($endDate)) {
                $progressBarText = "Prescription completed on " . $endDate->format('F j, Y');
            } else {
                $progressBarText = "Prescription starts on " . $startDate->format('F j, Y');
            }
        }
        // --- Doctor's Notes ---
        $doctorNotes = DoctorNote::where('patient_id', $patient->id)
                            ->orderBy('created_at', 'desc')
                            ->take(5) // Get the latest 2 notes
                            ->with('doctor') // Eager load the doctor (User) relationship
                           ->get();
        // --- Recovery Progress (Patient Measurements) ---
        $latestBloodPressure = PatientMeasurement::where('patient_id', $patient->id)
                                                ->where('measurement_type', 'Blood Pressure')
                                                ->orderBy('measured_at', 'desc')
                                                ->first();
        $latestWeight = PatientMeasurement::where('patient_id', $patient->id)
                                        ->where('measurement_type', 'Weight')
                                        ->orderBy('measured_at', 'desc')
                                        ->first();
        $latestSteps = PatientMeasurement::where('patient_id', $patient->id)
                                        ->where('measurement_type', 'Daily Steps')
                                        ->orderBy('measured_at', 'desc')
                                        ->first();

        // --- Fetch Notifications ---
        // Get all notifications for the authenticated user (patient's user)
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        // Get count of unread notifications for a badge
        $unreadNotificationsCount = $user->unreadNotifications->count();


        // Pass all data to the view
        return view('Patient.dashboard', compact( // Make sure this is 'Patient.dashboard' (capital P)
            'user',
            'patient',
            'todayMedications',
            'nextAppointment',
            'allAppointments',
            'progressBarText',
            'doctorNotes',
            'latestBloodPressure',
            'latestWeight',
            'latestSteps',
            'notifications',
            'unreadNotificationsCount'
        ));
    }

    /**
     * Mark a specific medication dose as taken.
     *
     * @param  \App\Models\MedicationDose  $medicationDose
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
   

    
     * Display all notifications for the authenticated patient.
     */
public function markMedicationTaken(MedicationDose $medicationDose, Request $request)
    {
        // Security check: Ensure the medication dose belongs to the authenticated patient
        if ($medicationDose->patient_id !== Auth::user()->patientProfile->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $isTaken = $request->input('is_taken');

        $medicationDose->is_taken = $isTaken;
        $medicationDose->taken_at = $isTaken ? Carbon::now() : null; // Set taken_at if marked taken, null if unchecked
        $medicationDose->save();

        return response()->json(['success' => true, 'message' => 'Medication status updated successfully.']);
    }



    public function indexNotifications()
    {
        $user = Auth::user();
        $patient = $user->patientInfo;

        if (!$patient) {
            return redirect()->route('patient.dashboard')->with('error', 'Patient profile not found.');
        }

        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        // Mark all notifications as read when the user views the notifications page
        $user->unreadNotifications->markAsRead();

        return view('Patient.notifications.index', compact('user', 'patient', 'notifications')); // Corrected view path
    }
} 