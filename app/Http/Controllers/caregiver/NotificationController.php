<?php

namespace App\Http\Controllers\Caregiver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the caregiver.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // You will fetch notifications for the authenticated caregiver here
        // Example: $notifications = Auth::user()->notifications()->latest()->get();
        // return view('caregiver.notifications.index', compact('notifications'));

        return view('caregiver.notifications.index');
    }
}