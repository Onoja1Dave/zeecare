<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\User;
use App\Models\MedicationDose;
use App\Models\DoctorNote;
use Carbon\Carbon;
use App\Models\Message; 
use App\Models\Conversation; 

class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Initialize variables to default empty states or reasonable defaults
        // These will be overridden if a patientRecord exists
        $nextAppointment = null;
        $todayMedications = collect();
        $doctorNotes = collect();
        $allAppointments = collect();
        $medicationAdherencePercentage = 0; // Default adherence
        $latestBloodPressure = null;
        $latestWeight = null;
        $latestSteps = null;

        // --- Fetch all patient-specific data only if a patientRecord exists ---
        if ($user->patientRecord) {
            $patient = $user->patientRecord; // <-- CRUCIAL: $patient is defined here once

            // Fetch the next upcoming appointment for the logged-in patient
            $nextAppointment = $patient->appointments()
                                                    ->where('appointment_datetime', '>=', now())
                                                    ->orderBy('appointment_datetime', 'asc')
                                                    ->first();

            // Fetch medication doses for today for the logged-in patient
            $todayMedications = $patient->medicationDoses()
                                                    ->whereDate('scheduled_at', Carbon::today()) // Use Carbon::today()
                                                    ->orderBy('scheduled_at', 'asc')
                                                    ->get();

            // Fetch doctor notes for the logged-in patient
            $doctorNotes = DoctorNote::where('patient_id', $patient->id)
                                    ->with('doctor')
                                    ->latest()
                                    ->get();

            // Fetch all upcoming appointments for the patient
            $allAppointments = $patient->appointments()
                                                    ->where('appointment_datetime', '>=', now())
                                                    ->orderBy('appointment_datetime', 'asc')
                                                    ->get();

            // Medication Adherence Calculation (moved inside this block)
            $totalDosesScheduled = $patient->medicationDoses()
                                           ->whereBetween('scheduled_at', [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()])
                                           ->count();
            $totalDosesTaken = $patient->medicationDoses()
                                       ->whereBetween('scheduled_at', [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()])
                                       ->where('is_taken', true)
                                       ->count();
            if ($totalDosesScheduled > 0) {
                $medicationAdherencePercentage = round(($totalDosesTaken / $totalDosesScheduled) * 100);
            }
        }
        // --- End of patient-specific data fetching ---


        // --- Dynamic Follow-up Status Message based on time of day (no change here) ---
        $currentTime = Carbon::now(config('app.timezone'));
        $followUpStatusText = "Your health journey progress is on track!"; // Default message

        if ($currentTime->hour >= 5 && $currentTime->hour < 12) {
            $followUpStatusText = "Good morning! Time to start your day strong.";
            $followUpDetailsText = "Remember your health goals for today.";
        } elseif ($currentTime->hour >= 12 && $currentTime->hour < 18) {
            $followUpStatusText = "Good afternoon! Keep up the great work.";
            $followUpDetailsText = "Don't forget your mid-day routines.";
        } else { // Evening
            $followUpStatusText = "Good evening! Time to wind down and rest.";
            $followUpDetailsText = "Ensure you're ready for a good night's sleep.";
        }
        // --- END Dynamic Follow-up Status ---


        // --- Calculate unread notifications count (no change here) ---
        $unreadNotificationsCount = $user->unreadNotifications->count();
        $notifications = $user->notifications()->latest()->take(5)->get();
        // --- END Unread Notifications ---

        $unreadMessagesCount = 0; // Initialize to 0
        if ($user->patientRecord) { // Ensure patient record exists before querying conversations
            $unreadMessagesCount = Message::whereHas('conversation', function ($query) use ($user) {
                                        $query->where('patient_id', $user->patientRecord->id);
                                    })
                                    ->where('sender_id', '!=', $user->id) // Messages sent by doctor (not current patient)
                                    ->whereNull('read_at') // That have not been read by the patient
                                    ->count();
        }

        // Pass all required variables to the view
        return view('patient.dashboard', compact(
            'user',
            'nextAppointment',
            'todayMedications',
            'doctorNotes',
            'allAppointments',
            'medicationAdherencePercentage',
            'latestBloodPressure',
            'latestWeight',
            'latestSteps',
            'followUpStatusText',
            'followUpDetailsText',
            'unreadNotificationsCount',
            'notifications' ,
            'unreadMessagesCount'
        ));
    }
}