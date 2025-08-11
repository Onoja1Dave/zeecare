<?php

namespace App\Http\Controllers\Caregiver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Patient; // Still needed for composeNew, but not for index query
use App\Models\User;

class MessageController extends Controller
{
    /**
     * Display a listing of messages for conversations between the authenticated caregiver and doctors.
     * (Excludes patient-doctor conversations).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $caregiverId = Auth::id();

        // Fetch conversations where the caregiver is a direct participant (caregiver_id is not null and matches current user)
        $conversations = Conversation::where('caregiver_id', $caregiverId)
                                     ->whereNotNull('doctor_id') // Ensure it's a caregiver-doctor conversation
                                     ->with(['doctor', 'messages.sender']) // Eager load the doctor and messages with senders
                                     ->orderBy('updated_at', 'desc') // Order by last message activity
                                     ->get();

        // We'll prepare a list of the latest message for each conversation to display in the index
        $latestMessages = [];
        foreach ($conversations as $conversation) {
            $latestMessages[] = [
                'conversation' => $conversation,
                'last_message' => $conversation->messages->sortByDesc('created_at')->first(),
                'unread_count' => $conversation->unreadMessagesCountForUser($caregiverId), // Re-use the method from Conversation model
            ];
        }

        return view('caregiver.messages.index', ['latestMessages' => collect($latestMessages)]);
    }

    // ... (composeNew() method remains the same as it correctly filters doctors) ...

    /**
     * Display a specific Doctor-Caregiver conversation thread.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showConversation(Conversation $conversation)
    {
        $caregiverId = Auth::id();
        $doctor = null; // Initialize doctor to null

        // Authorization: Ensure this conversation is a Doctor-Caregiver chat
        // AND the current caregiver is a participant.
        if ($conversation->caregiver_id !== $caregiverId || $conversation->patient_id !== null) {
            return redirect()->route('caregiver.messages.index')->with('error', 'You are not authorized to view this conversation.');
        }

        // Mark messages as read for the current caregiver (where caregiver is receiver and not sender)
        $conversation->messages()
                     ->where('sender_id', '!=', $caregiverId)
                     ->whereNull('read_at')
                     ->update(['read_at' => now()]);

        // Eager load messages with sender and ensure they are ordered correctly for display
        $conversation->load(['messages.sender' => function ($query) {
            $query->orderBy('created_at', 'asc'); // Order messages from oldest to newest for chronological display
        }]);

        $doctor = $conversation->doctor; // Get the doctor participant

        return view('caregiver.messages.show', compact('conversation', 'doctor'));
    }

    /**
     * Store a new message in the specified conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Conversation  $conversation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $caregiverId = Auth::id();
        // Authorization: Ensure this conversation is a Doctor-Caregiver chat
        // AND the current caregiver is a participant.
        if ($conversation->caregiver_id !== $caregiverId || $conversation->patient_id !== null) {
            return redirect()->route('caregiver.messages.index')->with('error', 'You are not authorized to send messages in this conversation.');
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $caregiverId,
            'content' => $request->input('content'),
            'read_at' => null, // New messages are unread by default
        ]);

        return redirect()->back()->with('success', 'Message sent!');
    }

    /**
 * Find an existing Doctor-Caregiver conversation or create a new one.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function findOrCreateConversation(Request $request)
{
    $caregiverId = Auth::id();
    $doctorId = $request->input('doctor_id');

    // Validation: Ensure a valid doctor_id is provided and that doctor is associated with caregiver's patients
    $request->validate([
        'doctor_id' => [
            'required',
            'exists:users,id',
            // Custom rule or logic to ensure this doctor is associated with current caregiver's patients
            function ($attribute, $value, $fail) use ($caregiverId) {
                $eligibleDoctorIds = Patient::where('caregiver_id', $caregiverId)->pluck('assigned_doctor_id')->unique();
                if (!$eligibleDoctorIds->contains($value)) {
                    $fail("The selected doctor is not associated with your assigned patients.");
                }
            },
        ],
    ]);

    // Try to find an existing conversation
    $conversation = Conversation::where('caregiver_id', $caregiverId)
                                ->where('doctor_id', $doctorId)
                                ->whereNull('patient_id') // Ensure it's specifically a Doctor-Caregiver chat
                                ->first();

    // If no conversation exists, create a new one
    if (!$conversation) {
        $conversation = Conversation::create([
            'caregiver_id' => $caregiverId,
            'doctor_id' => $doctorId,
            'patient_id' => null, // Explicitly null for Doctor-Caregiver conversations
        ]);
    }

    return redirect()->route('caregiver.messages.show', $conversation->id);
}

/**
     * Display a list of doctors the authenticated caregiver can message.
     * These are doctors assigned to the caregiver's patients.
     *
     * @return \Illuminate\View\View
     */
    public function composeNew() // <--- Ensure this exact spelling and casing
    {
       $caregiverId = Auth::id();

        // 1. Get all patients assigned to this caregiver
        $patients = Patient::where('caregiver_id', $caregiverId)->get();

        // 2. Get the unique doctor IDs from these patients
        $eligibleDoctorIds = $patients->pluck('assigned_doctor_id')->unique();

        // 3. Fetch the User models for these doctors
        $doctors = User::whereIn('id', $eligibleDoctorIds)
                       ->where('role', 'doctor')
                       ->orderBy('name')
                       ->get();

        return view('caregiver.messages.compose', compact('doctors'));
    }

}