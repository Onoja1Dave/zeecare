<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// No need to import App\Models\Notification as we're using the Notifiable trait

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the doctor.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $doctorUser = Auth::user();

        // Security check (optional, but good practice if not already handled by middleware)
        if (!$doctorUser->isDoctor()) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        // Fetch all notifications for the authenticated doctor user, ordered by latest
        // notifications() is a method provided by the Notifiable trait on your User model
        $notifications = $doctorUser->notifications()->latest()->get();

        return view('doctor.notifications.index', compact('notifications'));
    }
}