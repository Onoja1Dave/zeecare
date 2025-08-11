<?php

namespace App\Http\Controllers\Admin; // Note the Admin namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaregiverApplication; // Import the CaregiverApplication model
use Illuminate\Support\Str; // For generating token
use Carbon\Carbon; // For token expiry
use App\Models\User; // To check for existing users by email
use Illuminate\Support\Facades\Mail; // For sending email
use App\Mail\CaregiverRegistrationInvitation; // We will create this Mailable next

class CaregiverApplicationController extends Controller
{
    /**
     * Display a listing of caregiver applications.
     */
    public function index()
    {
        $applications = CaregiverApplication::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.caregiver_applications.index', compact('applications'));
    }

    /**
     * Approve a caregiver application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CaregiverApplication  $caregiverApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, CaregiverApplication $caregiverApplication)
    {
        // Only approve if it's pending
        if ($caregiverApplication->status === 'pending') {
            // Check if a user with this email already exists (should be handled by validation on application form, but good double-check)
            if (User::where('email', $caregiverApplication->email)->exists()) {
                return redirect()->back()->with('error', 'A user with this email already exists. Cannot approve application.');
            }

            // Generate a unique registration token
            $token = Str::random(60);
            $caregiverApplication->registration_token = $token;
            $caregiverApplication->token_expires_at = Carbon::now()->addHours(24); // Token valid for 24 hours
            $caregiverApplication->status = 'approved';
            $caregiverApplication->admin_notes = $request->input('admin_notes'); // If you add a notes field to the approve form
            $caregiverApplication->save();

            // Send registration invitation email
            Mail::to($caregiverApplication->email)->send(new CaregiverRegistrationInvitation($caregiverApplication));

            return redirect()->back()->with('success', 'Application approved and registration invitation sent to ' . $caregiverApplication->email);
        }

        return redirect()->back()->with('error', 'Only pending applications can be approved.');
    }

    /**
     * Reject a caregiver application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CaregiverApplication  $caregiverApplication
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, CaregiverApplication $caregiverApplication)
    {
        // Only reject if it's pending
        if ($caregiverApplication->status === 'pending') {
            $caregiverApplication->status = 'rejected';
            $caregiverApplication->admin_notes = $request->input('admin_notes'); // If you add a notes field to the reject form
            $caregiverApplication->save();

            return redirect()->back()->with('success', 'Application from ' . $caregiverApplication->name . ' has been rejected.');
        }

        return redirect()->back()->with('error', 'Only pending applications can be rejected.');
    }
}