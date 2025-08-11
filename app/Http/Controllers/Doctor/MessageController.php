<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Message; 
use App\Models\User;


class MessageController extends Controller

{
    /**
     * Display a listing of messages/conversations for the doctor.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $doctorUser = Auth::user();

        if (!$doctorUser->isDoctor()) {
            // Assuming isDoctor() method exists on User model to check role
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $conversations = $doctorUser->doctorConversations()
                                // Load patient and patient's user for patient-doctor chats
                                // Also load caregiver for doctor-caregiver chats
                                ->with([
                                    'patient.user', // For Patient-Doctor conversations
                                    'caregiver',    // For Doctor-Caregiver conversations
                                    'messages' => function($query) {
                                        $query->orderBy('created_at', 'desc')->with('sender');
                                    }
                                ])
                                ->orderByDesc('updated_at')
                                ->get();

        return view('doctor.messages.index', compact('conversations'));
    }

    /**
     * Display a specific conversation and its messages.
     *
     * @param  \App\Models\Conversation  $conversation  Laravel will automatically inject the Conversation model
     * @return \Illuminate\View\View
     */
public function show(Conversation $conversation) // Make sure your route points to this 'show' method
{
    $doctorUser = Auth::user();

    // Security check: Ensure this doctor is part of this conversation
    if ($conversation->doctor_id !== $doctorUser->id) {
        abort(403, 'Unauthorized access to this conversation.');
    }

    // Load all messages for this conversation, eager load sender, patient, AND CAREGIVER
    $conversation->load(['messages.sender', 'patient.user', 'caregiver']); // <--- ADDED 'caregiver' here

    // Determine the other participant (patient or caregiver) for display in the view
    $otherParticipant = null;
    if ($conversation->patient) {
        $otherParticipant = $conversation->patient->user; // Participant is the patient's user model
    } elseif ($conversation->caregiver) {
        $otherParticipant = $conversation->caregiver; // Participant is the caregiver user model
    }

    // Mark unread messages (sent by the other party) as read by the doctor
    $conversation->messages()
                 ->where('sender_id', '!=', $doctorUser->id)
                 ->whereNull('read_at')
                 ->update(['read_at' => now()]);

    // Return the view, compacting both conversation and otherParticipant
    return view('doctor.messages.show', compact('conversation', 'otherParticipant'));
}

    /**
     * Store a new message within a specific conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request, Conversation $conversation)
    {
        $doctorUser = Auth::user();

        // Security check: Ensure this doctor is part of this conversation
        if ($conversation->doctor_id !== $doctorUser->id) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the message content
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Create the new message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $doctorUser->id, // The current doctor is the sender
            'content' => $request->input('content'),
            'read_at' => null, // New messages are initially unread by the recipient
        ]);

        // Optionally, update the conversation's updated_at timestamp to bring it to the top of the list
        $conversation->touch();

        // Redirect back to the conversation page
        return redirect()->route('doctor.messages.show', $conversation->id)
                         ->with('success', 'Message sent!');
    }

/**
     * Show the form for creating a new message/conversation with a specific patient.
     * @param  \App\Models\Patient|null  $patient
     * @return \Illuminate\View\View
     */
    public function create(Patient $patient = null) // Make $patient optional as per route {patient?}
    {
        $doctorUser = Auth::user();

        // Optional: Add authorization/validation here
        // If a patient is provided, ensure this doctor is associated with them
        if ($patient && !$doctorUser->patients->contains($patient->id)) {
            // Or if using assigned_doctor_id directly:
            // if ($patient->assigned_doctor_id !== $doctorUser->id) {
            //    abort(403, 'Unauthorized to message this patient.');
            // }
            // For now, let's assume they are linked if patient_id is passed.
        }

        // If no patient is pre-selected, you might want to load a list of patients for the dropdown
        $patients = $doctorUser->patients; // Assuming doctorUser has a 'patients' relationship

        return view('doctor.messages.create', compact('patient', 'patients'));
    }


    /**
     * Store a newly created conversation and its first message.
     * This method is for starting a *new* conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $doctorUser = Auth::user();

        // Validate the request data
        $request->validate([
            'patient_id' => [
                'required',
                'exists:patients,id',
                // Optional: Add a rule to ensure the patient is assigned to this doctor
                // function ($attribute, $value, $fail) use ($doctorUser) {
                //     if (!$doctorUser->patients->contains($value)) {
                //         $fail('The selected patient is not assigned to you.');
                //     }
                // },
            ],
            'content' => 'required|string|max:1000',
        ]);

        $patient = Patient::findOrFail($request->patient_id);

        // Check if a conversation already exists between this doctor and patient
        $conversation = Conversation::where('doctor_id', $doctorUser->id)
                                    ->where('patient_id', $patient->id)
                                    ->first();

        if (!$conversation) {
            // Create a new conversation if one doesn't exist
            $conversation = Conversation::create([
                'doctor_id' => $doctorUser->id,
                'patient_id' => $patient->id,
                'last_message_at' => now(), // Initialize last message timestamp
            ]);
        } else {
            // If conversation exists, just touch it to update 'updated_at'
            $conversation->touch();
        }

        // Create the first message within the conversation
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $doctorUser->id, // Doctor is the sender
            'content' => $request->input('content'),
            'read_at' => null, // Initially unread by the recipient
        ]);

        return redirect()->route('doctor.messages.show', $conversation->id)
                         ->with('success', 'Conversation started and message sent!');
    }

/**
     * Finds an existing conversation with a patient or creates a new one, then redirects to the conversation.
     * This will be the new target for "Message Patient" links.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getOrCreateConversation(Patient $patient)
    {
        $doctorUser = Auth::user();

        // Optional security check: Ensure this doctor is authorized to message this patient
        // For example, if the patient must be assigned to this doctor:
        if (!$doctorUser->patients->contains($patient->id)) { // Assumes doctorUser has a 'patients' relationship
            // Or if you use a direct assigned_doctor_id column on the patient:
            // if ($patient->assigned_doctor_id !== $doctorUser->id) {
            abort(403, 'Unauthorized to message this patient.');
        }

        // Try to find an existing conversation between this doctor and patient
        $conversation = Conversation::where('doctor_id', $doctorUser->id)
                                    ->where('patient_id', $patient->id)
                                    ->first();

        // If no conversation exists, create a new one
        if (!$conversation) {
            $conversation = Conversation::create([
                'doctor_id' => $doctorUser->id,
                'patient_id' => $patient->id,
                'last_message_at' => now(), // Initialize timestamp
            ]);
            // Optional: You might want to create a default "welcome" message here
            // Message::create([
            //     'conversation_id' => $conversation->id,
            //     'sender_id' => $doctorUser->id,
            //     'content' => "Hello, I'm your doctor. How can I help you today?",
            //     'read_at' => null,
            // ]);
        }

        // Redirect directly to the show method for this conversation
        return redirect()->route('doctor.messages.show', $conversation->id);
    }


}