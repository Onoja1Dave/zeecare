<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicationDose;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Make sure Carbon is imported if you're using Carbon::now() elsewhere or just 'now()' helper

class MedicationDoseController extends Controller
{
    public function markTaken(Request $request, MedicationDose $medicationDose)
    {
        $user = Auth::user();

        // --- IMPORTANT: Refined Authorization Check ---
        if ($user->id !== $medicationDose->patient->user_id) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            return redirect()->back()->with('error', 'You are not authorized to mark this dose.');
        }

        // Check if the medication is already taken to prevent redundant updates
        if ($medicationDose->status === 'taken') { // Using status for check now
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Medication already marked as taken.']);
            }
            return redirect()->back()->with('info', 'Medication already marked as taken.');
        }

      \Log::info('PHP Default Timezone (date_default_timezone_get):', ['timezone' => date_default_timezone_get()]);
        \Log::info('Carbon::now() Time (configured app timezone):', [
            'time' => Carbon::now()->toDateTimeString(),
            'timezone_name' => Carbon::now()->timezoneName,
            'offset' => Carbon::now()->offset
        ]);


        $medicationDose->update([
            'status' => 'taken',
            'taken_at' => now(), // Using the global helper 'now()'
            'is_taken' => true,  // <-- ADD THIS LINE!
        ]);

        // You can remove the Carbon import at the top if only using now() helper.
        // It's still good practice to have Carbon imported if you plan to use its methods.

        // Prepare response based on request type
        if ($request->expectsJson()) {
            // Fetch the updated model to get the correct taken_at for the response,
            // or use the Carbon instance you just created.
            // Using now() helper, it's a Carbon instance, so we can format it directly.
            return response()->json([
                'success' => true,
                'message' => 'Medication dose marked as taken.',
                'taken_at' => Carbon::now()->format('h:i A') // Get current time again for response, or use the one from $medicationDose
            ]);
        }

        return redirect()->back()->with('success', 'Medication dose marked as taken.');
    }
}