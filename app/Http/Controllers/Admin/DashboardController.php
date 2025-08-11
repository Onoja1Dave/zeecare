<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Patient;
use App\Models\Caregiver;
use App\Models\CaregiverApplication; 
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Appointment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // --- 1. User Statistics ---
        $totalUsers = User::count();
        $totalPatients = Patient::count();
        $totalDoctors = User::where('role', 'doctor')->count();
        $totalPendingCaregiverApplications = CaregiverApplication::where('status', 'pending')->count();
        $totalApprovedCaregivers = User::where('role', 'caregiver')->count();


    
        // --- 2. Activity & Engagement ---
        $totalConversations = Conversation::count();
        $totalMessages = Message::count(); // All messages
        // $totalMessagesLast7Days = Message::where('created_at', '>=', Carbon::now()->subDays(7))->count(); // Example: messages last 7 days

        $totalAppointments = Appointment::count(); // All appointments
        $totalUpcomingAppointments = Appointment::where('appointment_datetime', '>=', now())->count();
        $totalCompletedAppointments = Appointment::where('appointment_datetime', '<', now())->where('status', 'completed')->count();


        // --- 3. Recent Activity Lists ---
        $latestUsers = User::latest()->take(5)->get(); 

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPatients',
            'totalDoctors',
            'totalApprovedCaregivers',
            'totalPendingCaregiverApplications',
            'totalConversations',
            'totalMessages',
            'totalAppointments',
            'totalUpcomingAppointments',
            'totalCompletedAppointments',
            'latestUsers'
        ));
    }
}