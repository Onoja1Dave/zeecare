<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PendingApprovalController extends Controller
{
    /**
     * Display a listing of pending approvals (e.g., for doctors, caregivers).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // You will fetch pending applications/users here
        // Example: $pendingUsers = \App\Models\User::where('status', 'pending')->get();
        // return view('admin.pending-approvals.index', compact('pendingUsers'));

        return view('admin.pending-approvals.index');
    }

    // Add approve and reject methods later as needed for POST routes
    // public function approve(Request $request, User $user) { /* ... */ }
    // public function reject(Request $request, User $user) { /* ... */ }
}