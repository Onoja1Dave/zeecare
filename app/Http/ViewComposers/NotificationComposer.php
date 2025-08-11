<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $unreadNotificationsCount = 0; // Default to 0

        // Check if a user is authenticated before trying to access their notifications
        if (Auth::check()) {
            // This assumes you are using Laravel's built-in Notifications
            // and your User model has a 'notifications' relationship.
            $unreadNotificationsCount = Auth::user()->unreadNotifications->count();
        }

        // Pass the variable to the view
        $view->with('unreadNotificationsCount', $unreadNotificationsCount);
    }
}