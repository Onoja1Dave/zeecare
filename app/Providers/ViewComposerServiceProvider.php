<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Import the View facade
use App\Http\ViewComposers\NotificationComposer; // Import your custom composer class

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Attach the NotificationComposer to specific views
        // We want this variable available in all your main layout files
        // and potentially individual dashboard files if they don't extend a layout.
        View::composer(
            [
                // 'layouts.*' will attach to layouts.admin, layouts.doctor, layouts.patient, etc.
                'layouts.*',
                // You can also list specific views if 'layouts.*' is too broad or insufficient:
                 'admin.dashboard',
                 'doctor.dashboard',
                 'caregiver.dashboard',
                 'patient.dashboard',
                // 'partials.navbar', // If your navbar is a separate partial
            ],
            NotificationComposer::class
        );
    }
}