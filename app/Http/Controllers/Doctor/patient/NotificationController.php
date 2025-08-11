<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    // You might have an index method for listing all notifications
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(10); // Example pagination

        return view('patient.notifications.index', compact('notifications'));
    }
/**
     * Mark a specific notification as read.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $notificationId
     * @return \Illuminate\Http\JsonResponse
     */

    public function markRead(Request $request, DatabaseNotification $notification)
    {
        // Ensure the authenticated user owns this notification
        if ($notification->notifiable_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $notification->markAsRead(); // Mark the notification as read

        return response()->json(['success' => true, 'message' => 'Notification marked as read!']);
    }

    // You might also want a method to mark all as read
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'All notifications marked as read!']);
    }
}