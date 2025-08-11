<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Models\Message; // Make sure to import Message
use App\Models\Conversation; // Make sure to import Conversation

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Use a View Composer to pass data to the main application layout
        View::composer('layouts.app', function ($view) {
            $pendingMessages = 0;
            $unreadNotificationsCount = 0;

            if (Auth::check()) {
                $user = Auth::user();

                // Check if the user has a 'doctor' role (assuming a 'role' column or hasRole method)
                if ($user->role === 'doctor') { // Adjust 'role' check based on your User model
                    // Fetch pending messages for the doctor
                    $pendingMessages = Message::whereHas('conversation', function($query) use ($user) {
                                              $query->where('doctor_id', $user->id); // Assuming 'doctor_id' on conversations
                                          })
                                          ->where('sender_id', '!=', $user->id)
                                          ->whereNull('read_at')
                                          ->count();

                    // Fetch unread notifications for the doctor
                    $unreadNotificationsCount = $user->unreadNotifications->count();
                }
                // You might add similar logic for 'patient' role here if needed
                // else if ($user->role === 'patient') { ... }
            }

            // Pass these counts to the view
            $view->with([
                'pendingMessagesNav' => $pendingMessages,
                'unreadNotificationsNav' => $unreadNotificationsCount,
            ]);
        });
    }
}