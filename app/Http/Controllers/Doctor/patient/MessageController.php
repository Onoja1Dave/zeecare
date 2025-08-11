<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Message; // Make sure Message model is imported
use App\Models\User;    // Make sure User model is imported
use App\Models\Patient; // Make sure Patient model is imported
use App\Events\MessageSent;

class MessageController extends Controller
{
    /**
     * Display a listing of the patient's message conversations.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $patientUser = Auth::user();

        // Check if the authenticated user has an associated patient profile
        // This is important if your User model just has a 'role' and Patient is a separate model.
        if (!$patientUser->isPatient() || !$patientUser->patientProfile) {
            // Handle error: User is not a patient or doesn't have a patient profile
            // For now, redirect or show an error.
            return redirect()->route('patient.dashboard')->with('error', 'Access denied. You are not linked to a patient profile.');
        }

        $patient = $patientUser->patientProfile; // Get the associated Patient model instance

        // Eager load the 'doctor' relationship and the 'latestMessage' for efficiency
        $conversations = $patient->conversations()
                                 ->with(['doctor', 'latestMessage'])
                                 ->latest('updated_at') // Order by when conversation was last updated/messaged
                                 ->paginate(10);

        // This will help in displaying the correct participant name in the view
        foreach ($conversations as $conversation) {
            $conversation->other_participant = $conversation->doctor; // In a patient's view, the other participant is always the doctor
            $conversation->unread_count = $conversation->unreadMessagesCountForUser($patientUser->id);
        }

        return view('patient.messages.index', compact('conversations'));
    }

    /**
     * Display a specific conversation.
     *
     * @param \App\Models\Conversation $conversation
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Conversation $conversation)
    {
        $patientUser = Auth::user();

        // Ensure the authenticated patient is part of this conversation
        // And that they are a patient and have a patientProfile
        if (!$patientUser->isPatient() || !$patientUser->patientProfile || $conversation->patient_id !== $patientUser->patientProfile->id) {
            return redirect()->route('patient.messages.index')->with('error', 'Conversation not found or access denied.');
        }

        // Load messages and sender details, order by oldest first
        $conversation->load(['messages.sender']);

        // Mark all unread messages in this conversation as read for the current patient
        $conversation->messages()
                     ->whereNull('read_at')
                     ->where('sender_id', '!=', $patientUser->id) // Only mark messages sent by the other party as read
                     ->update(['read_at' => now()]);

                     
        return view('patient.messages.show', compact('conversation'));
    }

    /**
     * Send a new message within a conversation.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Conversation $conversation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $patientUser = Auth::user();

        // Ensure the authenticated patient is part of this conversation
        if (!$patientUser->isPatient() || !$patientUser->patientProfile || $conversation->patient_id !== $patientUser->patientProfile->id) {
return response()->json(['error' => 'Cannot send message. Conversation not found or access denied.'], 403);
        }
        $message = $conversation->messages()->create([
            'sender_id' => $patientUser->id,
            'content' => $request->input('content'),
        ]);

// Eager load sender to include name in response for JS
        $message->load('sender');

        // Dispatch the event for real-time broadcasting
       // broadcast(new MessageSent($message, $conversation))->toOthers(); // Don't send to self if using AJAX append

        // Return a JSON response for AJAX
    



        // Optionally, update conversation's updated_at timestamp to bring it to top of list
        $conversation->touch();

        return redirect()->back()->with('success', 'Message sent!');
    }


    /**
     * Start a new conversation with the patient's assigned doctor.
     * Or find existing conversation.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startConversationWithPrescribingDoctor()
    {
        $patientUser = Auth::user();

        if (!$patientUser->isPatient() || !$patientUser->patientProfile) {
            return redirect()->route('patient.dashboard')->with('error', 'Access denied. You are not linked to a patient profile.');
        }

        $patient = $patientUser->patientProfile;

        // Assuming Patient model has 'assigned_doctor_id' if that's how doctor is assigned.
        // OR you have a mechanism to know who the patient's primary doctor is.
        // For this example, let's assume the patient has an 'assigned_doctor_id' in their 'patients' table.
        // If not, you need to adjust how you find the doctor.

        // If 'assigned_doctor_id' is NOT on the Patient model, but implicit in Conversation (first doctor they conversed with):
        // You'll need a different way to identify the doctor.
        // For simplicity, let's assume patient->assigned_doctor_id is available.
        // If not, we might need a doctor selection UI or to start with the first doctor in their existing conversations.

        // For now, let's assume the patient has an assigned doctor ID available.
        // If they don't, you'll need to define how a patient knows their doctor (e.g., from an 'assigned_doctor_id' in the 'patients' table).
        // Let's create a placeholder for 'assignedDoctor' in Patient model:
        $assignedDoctor = $patient->assignedDoctor; // This relies on a relationship in Patient model

        if (!$assignedDoctor) {
            return redirect()->back()->with('error', 'No assigned doctor found for starting a conversation.');
        }

        // Find existing conversation between this patient and this doctor
        $conversation = Conversation::where('patient_id', $patient->id)
                                    ->where('doctor_id', $assignedDoctor->id)
                                    ->first();

        if (!$conversation) {
            // Create a new conversation if one doesn't exist
            $conversation = Conversation::create([
                'patient_id' => $patient->id,
                'doctor_id' => $assignedDoctor->id,
            ]);
        }

        return redirect()->route('patient.messages.show', $conversation->id);
    }
}