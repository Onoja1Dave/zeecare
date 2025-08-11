<?php

namespace App\Http\Controllers\Caregiver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Task;
use App\Models\Appointment;
use App\Models\Alert;
use App\Models\Message;
use App\Models\Conversation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $caregiver = Auth::user();

        if (!$caregiver || !method_exists($caregiver, 'patientsUnderCare')) {
             return redirect()->route('login')->with('error', 'Caregiver profile not fully set up or not logged in.');
        }

        // --- Fetch Patients under Care ---
        $patients = $caregiver->patientsUnderCare()
            ->with([
                'alerts' => function ($query) {
                    $query->where('is_resolved', false);
                },
                'tasks' => function ($query) {
                    $query->whereDate('due_at', Carbon::today())
                          ->orWhere('is_completed', false);
                },
                'prescriptions',
                'appointments' => function ($query) {
                    $query->where('appointment_datetime', '>=', Carbon::now())
                          ->where('appointment_datetime', '<=', Carbon::now()->addDays(7))
                          ->orderBy('appointment_datetime');
                },
                'assignedDoctor'
            ])
            ->get();

        $totalPatients = $patients->count();

        // --- Alerts Count (used for Hero Section & all other alert badges) ---
        $newAlertsCount = Alert::where('caregiver_id', $caregiver->id)
                                 ->where('is_resolved', false)
                                 ->count();

        // --- Pending Tasks for Hero Section & Dashboard Card ---
        $pendingTasksCount = Task::whereIn('patient_id', $patients->pluck('id'))
                                 ->where('is_completed', false)
                                 ->count();

        // --- Pending Tasks for List ---
        $pendingTasksList = Task::whereIn('patient_id', $patients->pluck('id'))
                                ->where('is_completed', false)
                                ->orderBy('due_at', 'asc')
                                ->limit(5)
                                ->get();

        // --- Upcoming Appointments for List ---
        $upcomingAppointmentsList = Appointment::whereIn('patient_id', $patients->pluck('id'))
            ->where('appointment_datetime', '>=', Carbon::now())
            ->where('appointment_datetime', '<=', Carbon::now()->addDays(7)) // Next 7 days
            ->orderBy('appointment_datetime')
            ->get();

        $upcomingAppointmentsCount = $upcomingAppointmentsList->count();

        // --- Urgent Alerts (for dedicated dashboard section list) ---
        $urgentAlerts = Alert::where('caregiver_id', $caregiver->id)
                             ->where('is_resolved', false)
                             ->with('patient.user')
                             ->latest()
                             ->get();

        // --- Today's Snapshot Data ---
        $todayTasks = Task::whereIn('patient_id', $patients->pluck('id'))
                          ->whereDate('due_at', Carbon::today())
                          ->orderBy('due_at', 'asc')
                          ->get();

        $todayTasksCount = $todayTasks->count();
        $todayCompletedTasksCount = $todayTasks->where('is_completed', true)->count();
        $nextTaskToday = $todayTasks->where('is_completed', false)->sortBy('due_at')->first();

        $todayAppointments = Appointment::whereIn('patient_id', $patients->pluck('id'))
                                       ->whereDate('appointment_datetime', Carbon::today())
                                       ->orderBy('appointment_datetime', 'asc')
                                       ->get();
        $todayAppointmentsCount = $todayAppointments->count();
        $nextAppointmentToday = $todayAppointments->sortBy('appointment_datetime')->first();

        // --- Daily Task Progress (Percentage) ---
        $tasksCompletedTodayPercentage = 0;
        if ($todayTasksCount > 0) {
            $tasksCompletedTodayPercentage = round(($todayCompletedTasksCount / $todayTasksCount) * 100);
        }

        // --- Fetch Unread Messages ---
        $caregiverPatientIds = $patients->pluck('id');
        $relevantConversationIds = Conversation::whereIn('patient_id', $caregiverPatientIds)->pluck('id');

        $pendingMessagesNav = Message::whereIn('conversation_id', $relevantConversationIds)
                             ->where('sender_id', '!=', $caregiver->id)
                             ->whereNull('read_at')
                             ->count();

        return view('caregiver.dashboard', compact(
            'caregiver',
            'totalPatients',
            'patients',
            'newAlertsCount',             // Now the single source for new/unread alerts count
            'pendingTasksCount',
            'pendingTasksList',
            'upcomingAppointmentsCount',
            'upcomingAppointmentsList',
            'urgentAlerts',               // Collection of urgent alerts for the list
            'pendingMessagesNav',
            'todayTasksCount',
            'todayCompletedTasksCount',
            'nextTaskToday',
            'todayAppointmentsCount',
            'nextAppointmentToday',
            'tasksCompletedTodayPercentage'
        ));
    }
}