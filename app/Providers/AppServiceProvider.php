<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Don't forget to import View facade
use Illuminate\Support\Facades\Auth; // Don't forget to import Auth facade
use App\Models\User; // Don't forget to import User model

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer for caregiver layout
        View::composer('layouts.caregiver', function ($view) {
            $pendingMessagesNav = 0; // Default to 0

            if (Auth::check()) {
                $user = Auth::user();
                if ($user->isCaregiver()) { // Only calculate for caregivers using this layout
                    $pendingMessagesNav = $user->getTotalUnreadMessagesCount();
                }
            }
            $view->with('pendingMessagesNav', $pendingMessagesNav);

            // You can also add unreadNotificationsNav here if you have notification logic
            // (assuming your unreadNotificationsNav is calculated similarly for caregivers)
            // $unreadNotificationsNav = 0;
            // if (Auth::check() && $user->isCaregiver() && $user->unreadNotifications) {
            //     $unreadNotificationsNav = $user->unreadNotifications->count(); // Or specific method if defined
            // }
            // $view->with('unreadNotificationsNav', $unreadNotificationsNav);
        });

        // View Composer for doctor layout (assuming you have one with a similar nav structure)
        View::composer('layouts.doctor', function ($view) {
            $pendingMessagesNav = 0;
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->isDoctor()) { // Only calculate for doctors using this layout
                     $pendingMessagesNav = $user->getTotalUnreadMessagesCount();
                }
            }
            $view->with('pendingMessagesNav', $pendingMessagesNav);

            // (Add notification logic for doctors here if needed)
        });

        // If you have other common layouts (e.g., 'layouts.app' that both use),
        // you might create a composer for that as well.
    }
}