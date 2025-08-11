<?php

namespace App\Providers;



use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // API routes
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // ALL WEB routes should be defined within this 'web' middleware group
            Route::middleware('web')
                ->group(function () {
                    // Load your default web routes
                    require base_path('routes/web.php');

                    // Load your custom web-based role-specific routes here
                    Route::prefix('admin')
                        ->name('admin.')
                        ->group(base_path('routes/admin.php'));

                    Route::prefix('doctor')
                        ->name('doctor.')
                        ->group(base_path('routes/doctor.php'));

                    Route::prefix('caregiver')
                        ->name('caregiver.')
                        ->group(base_path('routes/caregiver.php'));

                    Route::prefix('patient')
                        ->group(base_path('routes/patient.php'));
                }); // <-- This brace closes the 'web' middleware group
        }); // <-- This brace closes the main routes() function
    }
}