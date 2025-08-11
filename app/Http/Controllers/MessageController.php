<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Patient; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prescription;
use App\Events\MessageSent;

class MessageController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    /**
     * Display a listing of the patient's conversations.
     */

    /**
     * Display a listing of the patient's conversations.
     */
    public function index()
    {
        $patient = Auth::user()->patientInfo;

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient profile not found.');
        }

        // Get all conversations for the authenticated patient, ordered by the latest message
        $conversations = $patient->conversations()
                                 ->with('doctor') // Eager load the doctor (User) for each conversation
                                 ->with(['messages' => function($query) {
                                     $query->latest()->limit(1); // Get only the latest message for display in the list
                                 }])
                                 ->get();

        return view('messages.index', compact('conversations'));
    }

    /**
     * Display the specified conversation (chat thread).
     */
    public function show(Conversation $conversation)
    {
        // Ensure the logged-in patient owns this conversation
        if ($conversation->patient_id !== Auth::user()->patientInfo->id) {
            abort(403, 'Unauthorized action.');
        }

        // Load all messages for the conversation, eager load the sender
        $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        // Mark messages as read by the patient (if they haven't been)
        $messages->whereNull('read_at')->where('sender_id', '!=', Auth::id())->each(function ($message) {
            $message->update(['read_at' => now()]);
        });


        return view('messages.show', compact('conversation', 'messages'));
    }


/**
     * Handles sending a message (likely for patients, based on route name).
     */
    public function send(Request $request, Conversation $conversation)
    {
        // 1. Get the currently authenticated user
        $user = Auth::user();

        // Basic check: Ensure a user is actually logged in
        if (!$user) {
            // If no user is logged in, they shouldn't even be hitting this route.
            // This is a safety net.
            return redirect()->back()->with('error', 'You must be logged in to send messages.');
        }

        // 2. Robust Security check: Ensure the authenticated user (doctor OR patient) is part of this conversation
        // This check covers both if the logged-in user is the doctor of the conversation,
        // or if they are the patient associated with the conversation.
        if ($conversation->doctor_id !== $user->id && $conversation->patient->user->id !== $user->id) {
            abort(403, 'Unauthorized action. You are not part of this conversation.');
        }

        // 3. Validate the message content
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        // 4. Create and save the new message
        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->sender_id = $user->id; // Using the $user variable now
        $message->content = $request->input('content');
        $message->save();

        // 5. Dispatch the MessageSent event after saving
        // This sends the message through Soketi to other listening clients
        broadcast(new MessageSent($message, $user))->toOthers(); // Using the $user variable here

        // 6. Return JSON response for AJAX form submission
        return response()->json(['success' => true, 'message' => [
            'id' => $message->id,
            'content' => $message->content,
            'sender_id' => $user->id,
            'sender_name' => $user->name, // Ensure your User model has a 'name' attribute
            'created_at' => $message->created_at->format('H:i A, M d'), // Format for consistent display
            'read_at' => $message->read_at, // Will likely be null initially
        ]]);


   





 $message = $conversation->messages()->create([
            'sender_id' => Auth::id(), // The logged-in user is the sender
            'content' => $request->content,
        ]);
        
    }


public function startConversationWithPrescribingDoctor()
{
    $patient = Auth::user()->patientInfo;

    if (!$patient) {
        return redirect()->back()->with('error', 'Patient profile not found.');
    }

    // Find the latest prescription to determine the doctor
    $latestPrescription = Prescription::where('patient_id', $patient->id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();

    if (!$latestPrescription || !$latestPrescription->doctor_id) {
        return redirect()->back()->with('error', 'No prescribing doctor found for this patient.');
    }

    $doctor_id = $latestPrescription->doctor_id;

    // Find existing conversation or create a new one
    $conversation = Conversation::firstOrCreate(
        [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor_id,
        ],
        // Attributes to set if creating a new conversation (can be empty for now)
        []
    );

    // Redirect to the show method for this specific conversation
    return redirect()->route('messages.show', $conversation->id);
}




}