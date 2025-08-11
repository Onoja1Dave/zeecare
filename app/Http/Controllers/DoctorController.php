<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\User;
use App\Models\MedicationDose;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\DoctorNote;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use App\Notifications\NewDoctorNote;
use Carbon\Carbon;

// If you have a dedicated 'FollowUp' model, you might need to uncomment this:
// use App\Models\FollowUp;


class DoctorController extends Controller
{
    // Constructor to apply the auth middleware to the entire controller
    // and our custom check.role middleware
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.role:doctor'); // Only users with 'doctor' role can access methods in this controller
    }


    
    public function dashboard()
    {
        $doctorUser = Auth::user(); // The authenticated user is the doctor

        // Fetch patients assigned to this doctor, eager load their 'user' relationship
        $patients = $doctorUser->patients()->with('user')->get();

        // --- NEW: Fetch Data for Summary Cards ---
        // 1. Total Patients (count from existing patients collection)
        $totalPatients = $patients->count();

        // 2. Today's Appointments
        // Using 'appointment_datetime' for date comparison
        $todaysAppointments = Appointment::where('doctor_id', $doctorUser->id)
                                         ->whereDate('appointment_datetime', Carbon::today())
                                         ->count();

        // 3. Pending Messages - CORRECTED FOR YOUR SCHEMA
        // Finds messages that belong to conversations where this doctor is involved,
        // are sent by someone OTHER than this doctor (i.e., the patient),
        // and have not yet been marked as read.
        $pendingMessages = Message::whereHas('conversation', function($query) use ($doctorUser) {
                                      $query->where('doctor_id', $doctorUser->id);
                                  })
                                  ->where('sender_id', '!=', $doctorUser->id)
                                  ->whereNull('read_at')
                                  ->count();


        // 4. Active Follow-ups
        // This is based on DoctorNote model, counting notes created by this doctor's patients within the last 7 days.
        // Adjust this logic if you have a specific 'FollowUp' model or a different definition of 'active follow-ups'.
        $activeFollowUps = DoctorNote::whereHas('patient', function($query) use ($doctorUser) {
                                        $query->where('assigned_doctor_id', $doctorUser->id);
                                    })
                                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                                    ->count();


        // --- NEW: Fetch Data for Upcoming Appointments Tab ---
        // Fetch appointments for today and tomorrow, ordered by date and time
        // Using 'appointment_datetime' for both date comparisons and ordering.
        $upcomingAppointments = Appointment::where('doctor_id', $doctorUser->id)
                                          ->where(function($query) {
                                              $query->whereDate('appointment_datetime', Carbon::today())
                                                    ->orWhereDate('appointment_datetime', Carbon::tomorrow());
                                          })
                                          ->orderBy('appointment_datetime')
                                          ->get();
$unreadNotificationsCount = $doctorUser->unreadNotifications->count();

        // --- NEW: Fetch recent Prescriptions ---
        $recentPrescriptions = Prescription::where('doctor_id', $doctorUser->id)
                                           ->with('patient.user') // Eager load patient and their user
                                           ->latest() // Order by created_at DESC
                                           ->take(5)  // Get the 5 most recent prescriptions
                                           ->get();




        // Prepare data for the view
        return view('doctor.dashboard', compact(
            'doctorUser',
            'patients',
            'totalPatients',
            'todaysAppointments',
            'pendingMessages',
            'activeFollowUps',
            'upcomingAppointments',
            'recentPrescriptions'
        ));
    }





    public function showPatientProfile(Patient $patient)
    {
        if (Auth::id() !== $patient->assigned_doctor_id) {
            return redirect()->route('doctor.dashboard')->with('error', 'You do not have permission to view this patient\'s profile.');
        }

        $patient->load('user', 'medicationDoses.prescription', 'appointments.doctor');

        return view('doctor.patient_profile', compact('patient'));
    }

    // --- NEW METHOD FOR ASSIGNING PATIENTS ---
    public function showAssignPatientForm()
    {
        $unassignedPatients = Patient::whereNull('assigned_doctor_id')
                                     ->with('user')
                                     ->get();

                               

        return view('doctor.assign_patients', compact('unassignedPatients'));
    }

    // --- NEW METHOD TO HANDLE ASSIGNMENT SUBMISSION ---
    public function assignPatient(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id', // Ensure the selected patient ID exists
        ]);

        $doctorUser = Auth::user(); // The logged-in doctor

        // Find the patient and assign the doctor's ID
        $patient = Patient::findOrFail($request->patient_id);

        // Add a check to prevent re-assigning if already assigned, or only assign if not already assigned to *this* doctor
        if ($patient->assigned_doctor_id !== null && $patient->assigned_doctor_id !== $doctorUser->id) {
             return redirect()->back()->with('error', 'This patient is already assigned to another doctor.');
        }

        $patient->assigned_doctor_id = $doctorUser->id; // Set the assigned doctor ID
        $patient->save(); // Save the change

        return redirect()->route('doctor.dashboard')->with('success', 'Patient assigned successfully!');

    }

    // --- NEW METHOD: storeAppointment (to handle form submission) ---
    public function storeAppointment(Request $request)
    {
        $doctorUser = Auth::user();

        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_datetime' => 'required|date|after_or_equal:now', // Must be in the future
            'notes' => 'nullable|string|max:1000',
        ]);

        // Security check: Ensure the selected patient is assigned to this doctor
        $patient = Patient::findOrFail($validatedData['patient_id']);
        if ($patient->assigned_doctor_id !== $doctorUser->id) {
            return redirect()->back()->with('error', 'You can only create appointments for patients assigned to you.');
        }

        $appointment = new Appointment();
        $appointment->patient_id = $validatedData['patient_id'];
        $appointment->doctor_id = $doctorUser->id; // Assign the current doctor
        $appointment->appointment_datetime = $validatedData['appointment_datetime'];
        $appointment->notes = $validatedData['notes'];
        $appointment->save();

        return redirect()->route('doctor.dashboard')->with('success', 'Appointment created successfully!');
    }

    public function showCreateAppointmentForm()
    {
        $doctorUser = Auth::user();
        $assignedPatients = $doctorUser->patients()->with('user')->get();

        
        return view('doctor.appointments.create', compact('assignedPatients'));
    }


    /**
     * Show the form for creating a new prescription for a specific patient.
     */
    public function createPrescriptionForm(Patient $patient)
    {
        // Security check: Ensure the logged-in doctor is the assigned doctor for this patient
        if (Auth::id() !== $patient->assigned_doctor_id) {
            return redirect()->route('doctor.dashboard')->with('error', 'You do not have permission to prescribe for this patient.');
        }

        // Eager load the patient's user info for the form
        $patient->load('user');

        return view('doctor.create_prescription', compact('patient'));
    }

    /**
     * Store a newly created prescription and its associated medication doses.
     */
    public function storePrescription(Request $request, Patient $patient)
    {
        // Security check: Ensure the logged-in doctor is assigned to this patient
        if (Auth::id() !== $patient->assigned_doctor_id) {
            return redirect()->route('doctor.dashboard')->with('error', 'You do not have permission to prescribe for this patient.');
        }

        // 1. Validate the form data
        $validatedData = $request->validate([
            'drug_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'frequency' => 'required|string|max:255',
            'duration' => 'required|integer|min:1', // Duration in days
        ]);
        // 2. Create the new Prescription record
        $prescription = new Prescription();
        $prescription->patient_id = $patient->id;
        $prescription->doctor_id = Auth::id();
        $prescription->drug_name = $validatedData['drug_name'];
        $prescription->dosage = $validatedData['dosage'];
        $prescription->frequency = $validatedData['frequency'];
        $prescription->duration = $validatedData['duration'];
        $prescription->save();

        // 3. Generate MedicationDose records based on frequency and duration (ENHANCED LOGIC)
        $startDate = Carbon::now();
        $numberOfDosesPerDay = 1;
        $specificTimes = []; // e.g., ['08:00', '14:00', '20:00']

        // Basic frequency parsing logic (you can make this more complex)
        $frequencyLower = strtolower($validatedData['frequency']);

        if (str_contains($frequencyLower, 'once daily') || str_contains($frequencyLower, 'once a day')) {
            $numberOfDosesPerDay = 1;
            $specificTimes = ['09:00']; // Example: Morning
        } elseif (str_contains($frequencyLower, 'twice daily') || str_contains($frequencyLower, 'two times a day')) {
            $numberOfDosesPerDay = 2;
            $specificTimes = ['09:00', '21:00']; // Example: Morning & Evening
        } elseif (str_contains($frequencyLower, 'three times daily') || str_contains($frequencyLower, 'thrice daily') ||  str_contains($frequencyLower, '3 times a day')) {
            $numberOfDosesPerDay = 3;
            $specificTimes = ['08:00', '14:00', '20:00']; // Example: Morning, Mid-day, Evening
        } elseif (str_contains($frequencyLower, 'four times daily') || str_contains($frequencyLower, '4 times a day')) {
            $numberOfDosesPerDay = 4;
            $specificTimes = ['06:00', '12:00', '18:00', '00:00']; // Example: Every 6 hours
        } elseif (preg_match('/every (\d+) hours/', $frequencyLower, $matches)) {
            $hours = (int) $matches[1];
            if ($hours > 0 && $hours <= 24) {
                $numberOfDosesPerDay = 24 / $hours;
                // Generate times based on interval
                for ($i = 0; $i < $numberOfDosesPerDay; $i++) {
                    $specificTimes[] = Carbon::createFromTime($i * $hours, 0, 0)->format('H:i');
                }
            }
        }
        // Add more specific parsing for "every other day", "weekly", etc. if needed.
        // For now, if no specific times are found, default to once daily at 9am.
        if (empty($specificTimes)) {
             $numberOfDosesPerDay = 1;
             $specificTimes = ['09:00'];
        }
        for ($day = 0; $day < $validatedData['duration']; $day++) {
            $currentDay = $startDate->copy()->addDays($day);

            foreach ($specificTimes as $time) {
                // Combine the current day's date with the specific time
                $scheduledDateTime = Carbon::parse($currentDay->format('Y-m-d') . ' ' . $time);

                // Ensure the scheduled date/time is not in the past if starting from now
                // However, for consistency, we'll schedule based on prescription start.
                // If you want to skip past doses for today, add logic here.
                $medicationDose = new MedicationDose();
                $medicationDose->prescription_id = $prescription->id;
                $medicationDose->patient_id = $patient->id;
                $medicationDose->scheduled_at = $scheduledDateTime->toDateTimeString();
                $medicationDose->status = 'scheduled';
                $medicationDose->is_taken = false;
                $medicationDose->save();
            }
        }

        return redirect()->route('doctor.patients.show', $patient->id)->with('success', 'Prescription created successfully!');
    }

    /**
     * * Store a new doctor's note for a specific patient.
     */
    public function storeDoctorNote(Request $request, Patient $patient)
    {
        // Security check: Ensure the logged-in doctor is assigned to this patient
        if (Auth::id() !== $patient->assigned_doctor_id) {
            return redirect()->route('doctor.dashboard')->with('error', 'You do not have permission to add notes for this patient.');
        }

        // Validate the note content
        $request->validate([
            'note_content' => 'required|string|max:1000', // We'll name the form field 'note_content'
        ]);

        $note = new DoctorNote();
        $note->patient_id = $patient->id;
        $note->doctor_id = Auth::id(); // The ID of the currently logged-in doctor
        $note->content = $request->input('note_content'); // Use 'content' as per your table schema
        $note->save();

        // --- NEW: Trigger the notification ---
        // We notify the patient's associated User model, as it has the Notifiable trait
        if ($patient->user) { // Ensure the patient has a linked user
            $patient->user->notify(new NewDoctorNote($note, $patient));
        }
        // --- END NEW ---

        return redirect()->route('doctor.patients.show', $patient->id)->with('success', 'Note added successfully!');
    }

    /**
     * Display a list of all prescriptions written by the logged-in doctor.
     */
    public function indexPrescriptions()
    {
        $doctorUser = Auth::user();

        // Fetch all prescriptions where the doctor_id matches the logged-in doctor's ID
        // Eager load the patient relationship and the patient's user relationship
        $prescriptions = Prescription::where('doctor_id', $doctorUser->id)
                                    ->with('patient.user') // Load patient details and their user details
                                    ->orderByDesc('created_at') // Show newest prescriptions first
                                    ->get();
        return view('doctor.prescriptions.index', compact('prescriptions'));
    }
    /**
     * Display a list of all appointments for the logged-in doctor.
     */
    public function indexAppointments()
    {
        $doctorUser = Auth::user();

        // Fetch all appointments where the doctor_id matches the logged-in doctor's ID
        // Eager load the patient relationship and the patient's user relationship
        $appointments = Appointment::where('doctor_id', $doctorUser->id)
                                    ->with('patient.user') // Load patient details and their user details
                                    ->orderBy('appointment_datetime') // Changed from appointment_dateTime for consistency
                                    ->paginate(5);

        return view('doctor.appointments.index', compact('appointments'));
    }

/**
     * Display a listing of the doctor's notifications.
     */
    public function indexNotifications()
    {
        $doctorUser = Auth::user();

        // Get all notifications for the doctor, paginate them, and mark unread ones as read
        $notifications = $doctorUser->notifications()->paginate(10); // Fetch all notifications, paginate for better UX

        // Mark all unread notifications as read as soon as the user views the page
        $doctorUser->unreadNotifications->markAsRead();

        // Update the count in the session for the View Composer to reflect changes immediately
        // This is a temporary fix, as the Composer should re-evaluate on refresh.
        // A more robust solution for real-time updates might involve Livewire or Inertia.
        session()->flash('unreadNotificationsCount', 0);


        return view('doctor.notifications.index', compact('notifications'));
    }

    /**
     * Mark all notifications for the authenticated doctor as read.
     */
    public function markAllNotificationsAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        // Flash a success message
        session()->flash('success', 'All notifications marked as read.');

        // Update the count in the session for the View Composer
        session()->flash('unreadNotificationsCount', 0);


        return redirect()->back(); // Redirect back to the notifications page or dashboard
    }

    /**
     * Mark a specific notification as read.
     */
    public function markNotificationAsRead(DatabaseNotification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if (Auth::id() === $notification->notifiable_id) {
            $notification->markAsRead();
            session()->flash('success', 'Notification marked as read.');
            // Update the count in the session after marking one as read
            session()->flash('unreadNotificationsCount', Auth::user()->unreadNotifications->count());

        } else {
            session()->flash('error', 'You are not authorized to mark this notification as read.');
        }

        return redirect()->back(); // Redirect back to the notifications page or dashboard
    }




    /**
     * Display a list of all conversations for the logged-in doctor.
     */
   
     public function indexMessages()
{
    $doctorUser = Auth::user();

    // Fetch conversations related to this doctor, with patient and their messages
    // Order by the latest message's timestamp to show most active chats first
    $conversations = Conversation::where('doctor_id', $doctorUser->id)
                                 ->with(['patient.user', 'messages']) // Eager load patient's user and all messages
                                 ->get()
                                 ->sortByDesc(function($conversation) {
                                     // Sort by the created_at of the latest message, or conversation created_at if no messages
                                     return $conversation->messages->max('created_at') ?? $conversation->created_at;
                                 });

    return view('doctor.messages.index', compact('conversations'));
}
    /**
     * Finds or creates a conversation between the logged-in doctor and a specific patient.
     * Redirects to the conversation view.
     */
    public function messagePatient(Patient $patient)
    {
        $doctorUser = Auth::user();
        // Security check: Ensure the logged-in doctor is assigned to this patient
        if ($doctorUser->id !== $patient->assigned_doctor_id) {
            return redirect()->route('doctor.dashboard')->with('error', 'You cannot message a patient not assigned to you.');
        }

        // Try to find an existing conversation
        // Important: We assume patient_id is always the patient, and doctor_id is always the doctor in 'conversations' table
        $conversation = Conversation::firstOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctorUser->id,
            ],
            // If it's created, these are the initial values (empty for now)
            []
        );

        // Redirect to the individual conversation view
        // We will create 'doctor.messages.show' route and method next
        return redirect()->route('doctor.messages.show', $conversation->id);
    }

    /**
     * Display a specific conversation.
     */
    public function showMessage(Conversation $conversation)
    {
        $doctorUser = Auth::user();

        // Security check: Ensure the logged-in doctor is part of this conversation
        // A doctor should only see conversations they are part of.
        if ($conversation->doctor_id !== $doctorUser->id) {
            return redirect()->route('doctor.messages.index')->with('error', 'You do not have permission to view this conversation.');
        }

        // Mark unread messages sent by the patient as read
        // Only mark messages where the sender is the patient, and it hasn't been read by this doctor yet.
        $conversation->messages()
                     ->where('sender_id', $conversation->patient->user->id)
                     ->whereNull('read_at')
                     ->update(['read_at' => now()]);

        // Load messages for the conversation, eager load the sender for display
        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        return view('doctor.messages.show', compact('conversation', 'messages'));
    }
    /**
     * Send a new message within a conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $doctorUser = Auth::user();

        // Security check: Ensure the logged-in doctor is part of this conversation
        if ($conversation->doctor_id !== $doctorUser->id) {
            return redirect()->back()->with('error', 'You do not have permission to send messages in this conversation.');
        }

        // Validate the message content
        $request->validate([
            'content' => 'required|string|max:2000', // Max 2000 characters for a message
        ]);

        // Create and save the new message
        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->sender_id = $doctorUser->id; // The logged-in doctor is the sender
        $message->content = $request->input('content');
        $message->save();

        // Dispatch the MessageSent event after saving
        // Pass the message and the sender (the doctor user)
        broadcast(new MessageSent($message, $doctorUser))->toOthers();

        // Redirect back to the same conversation page
       return response()->json(['success' => true, 'message' => [
            'id' => $message->id,
            'content' => $message->content,
            'sender_id' => $doctorUser->id,
            'sender_name' => $doctorUser->name, // Ensure $doctorUser->name is accessible
            'created_at' => $message->created_at->format('H:i A, M d'),
            'read_at' => $message->read_at,
        ]]);
    }
}