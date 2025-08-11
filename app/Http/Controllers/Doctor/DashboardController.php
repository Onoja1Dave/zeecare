<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Prescription;
use Carbon\Carbon;
use App\Models\Message; 

class DashboardController extends Controller
{
    public function index()
    {
        $doctorUser = Auth::user();

        // 1. Fetch Patients assigned to this doctor
        // Use patientsAssigned() relationship from User model
        $patients = $doctorUser->patientsAssigned()->with('user')->get();
        $totalPatients = $patients->count();

        // 2. Fetch Today's Appointments
        // Use doctorAppointments() relationship from User model
        $todaysAppointments = $doctorUser->doctorAppointments()
                                        ->whereDate('appointment_datetime', Carbon::today())
                                        ->count();

        // 3. Fetch Upcoming Appointments (for the tab: today and tomorrow)
        // Use doctorAppointments() relationship from User model
        $upcomingAppointments = $doctorUser->doctorAppointments()
                                        ->whereBetween('appointment_datetime', [
                                            Carbon::today()->startOfDay(),
                                            Carbon::tomorrow()->endOfDay()
                                        ])
                                        ->with('patient.user')
                                        ->orderBy('appointment_datetime')
                                        ->get();

        // 4. Fetch Recent Prescriptions
        // Use prescriptions() relationship from User model
        $recentPrescriptions = $doctorUser->prescriptions()
                                        ->with('patient.user')
                                        ->latest()
                                        ->take(5)
                                        ->get();

        // 5. Define placeholder/derived values for summary cards
        $activeFollowUps = $patients->where('status', 'active_followup')->count(); // Adapt based on how you define this
      
      
      $pendingMessages = 0; // Placeholder until messaging implemented
if ($doctorUser->isDoctor()) {
            $pendingMessagesNav = Message::whereHas('conversation', function ($query) use ($doctorUser) {
                                    // Filter messages belonging to conversations where this doctor is the doctor_id
                                    $query->where('doctor_id', $doctorUser->id);
                                })
                                ->where('sender_id', '!=', $doctorUser->id) // Message was not sent by the current doctor
                                ->whereNull('read_at') // Message has not been read (by the doctor)
                                ->count();
        }

$unreadNotificationsNav = $doctorUser->unreadNotifications->count();

        return view('doctor.dashboard', compact(
            'doctorUser',
            'activeFollowUps',
            'pendingMessages',
            'todaysAppointments',
            'totalPatients',
            'patients',
            'upcomingAppointments',
            'recentPrescriptions',
            'pendingMessagesNav',
            'unreadNotificationsNav'
        ));
    }
}