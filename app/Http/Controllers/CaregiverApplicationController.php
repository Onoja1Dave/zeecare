<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaregiverApplication;
use Illuminate\Support\Facades\Validator;

class CaregiverApplicationController extends Controller
{
    /**
     * Show the caregiver application form.
     *
     * @return \Illuminate\View\View
     */
    public function showApplicationForm()
    {
        return view('caregiver.caregiver_applications.apply');
    }

    /**
     * Store a new caregiver application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:caregiver_applications,email|unique:users,email', // Check both tables for uniqueness
            'contact_info' => 'required|string', // Assuming contact_info is required from your schema
            'experience' => 'nullable|string', // Matches your 'experience' column
            'reason' => 'nullable|string', // Matches your 'reason' column
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        CaregiverApplication::create([
            'name' => $request->name,
            'email' => $request->email, // NEW: Store the explicit email
            'contact_info' => $request->contact_info, // Use your existing column
            'experience' => $request->experience, // Use your existing column
            'reason' => $request->reason, // Use your existing column
            'status' => 'pending', // Default status
        ]);

        return redirect()->route('welcome')->with('success', 'Your caregiver application has been submitted successfully! We will review it and get back to you soon.');
    }

    // Admin methods (index, approve, reject) will be added later
}