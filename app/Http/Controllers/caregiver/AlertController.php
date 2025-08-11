<?php

namespace App\Http\Controllers\Caregiver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alert; // IMPORTANT: Make sure this Alert model is imported!
use Illuminate\Support\Facades\Auth; // IMPORTANT: Make sure Auth facade is imported!

class AlertController extends Controller
{
    /**
     * Display a listing of alerts for the caregiver.
     * (This is your existing index method, kept for completeness)
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // You can fetch alerts here if you want a separate page to list all alerts
        // $caregiver = Auth::user();
        // $alerts = Alert::where('caregiver_id', $caregiver->id)->orderBy('created_at', 'desc')->get();
        // return view('caregiver.alerts.index', compact('alerts'));
        
        return view('caregiver.alerts.index'); // Your original return view
    }

    /**
     * Mark a specific alert as resolved.
     *
     * @param  \App\Models\Alert  $alert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markResolved(Alert $alert)
    {
        // Ensure the alert belongs to the authenticated caregiver for security
        if ($alert->caregiver_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action: This alert is not assigned to you.');
        }

        $alert->is_resolved = true;
        $alert->resolved_at = now();
        $alert->save();

        return back()->with('success', 'Alert marked as resolved.');
    }

    /**
     * Mark all unresolved alerts for the current caregiver as resolved.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllResolved()
    {
        $caregiverId = Auth::id();

        // Mark all unresolved alerts for the current caregiver as resolved
        Alert::where('caregiver_id', $caregiverId)
             ->where('is_resolved', false)
             ->update([
                 'is_resolved' => true,
                 'resolved_at' => now(),
             ]);

        return back()->with('success', 'All pending alerts marked as resolved.');
    }
}