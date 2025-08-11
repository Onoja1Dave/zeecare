<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient; // Make sure to import Patient
use App\Models\Task;    // Make sure to import Task
use App\Models\Appointment; // Make sure to import Appointment
use App\Models\Alert;   // Make sure to import Alert
use Carbon\Carbon; // For date comparisons

class CaregiverController extends Controller
{
    /**
     * Show the caregiver dashboard with relevant data.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Get the authenticated caregiver user
        $caregiver = Auth::user();

        // Ensure the authenticated user is actually a caregiver
        if (!$caregiver || $caregiver->role !== 'caregiver') {
            // Handle unauthorized access, e.g., redirect or abort
            abort(403, 'Unauthorized. You must be a caregiver to access this dashboard.');
        }

        // --- Fetch Data for Dashboard ---

        // 1. Patients Under Care (for the specific caregiver)
        // Use the 'patientsUnderCare' relationship defined in the User model
        $patients = $caregiver->patientsUnderCare()->with(['primaryDoctor', 'prescriptions', 'medicationDoses', 'tasks', 'appointments', 'alerts'])
                                ->get(); // Eager load relationships for efficiency

        // 2. Summary Counts
        $totalPatients = $patients->count();

        // Tasks for today, specifically for this caregiver's patients
        $todayTasks = Task::where('caregiver_id', $caregiver->id)
                            ->whereDate('due_at', Carbon::today())
                            ->where('is_completed', false) // Only count incomplete tasks for today
                            ->with('patient') // Eager load patient details for tasks
                            ->get();
        $todayTasksCount = $todayTasks->count();


        // Upcoming appointments for this caregiver (within, say, the next 7 days)
        $upcomingAppointments = Appointment::where('caregiver_id', $caregiver->id)
                                        ->where('appointment_datetime', '>=', Carbon::now())
                                        ->where('appointment_datetime', '<=', Carbon::now()->addDays(7))
                                        ->with('patient') // Eager load patient details for appointments
                                        ->orderBy('appointment_datetime', 'asc')
                                        ->get();
        $upcomingAppointmentsCount = $upcomingAppointments->count();


        // Unread/Unresolved alerts for this caregiver (or their patients)
        $unreadAlerts = Alert::where('caregiver_id', $caregiver->id)
                                ->where('is_resolved', false)
                                ->with('patient') // Eager load patient details for alerts
                                ->orderBy('created_at', 'desc')
                                ->get();
        $unreadAlertsCount = $unreadAlerts->count();


        // Pass all fetched data to the view
        return view('caregiver.dashboard', compact(
            'caregiver',
            'patients',
            'totalPatients',
            'todayTasks',
            'todayTasksCount',
            'upcomingAppointments',
            'upcomingAppointmentsCount',
            'unreadAlerts',
            'unreadAlertsCount'
        ));
    }
}