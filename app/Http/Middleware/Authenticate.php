<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            if ($request->routeIs('admin.*')) { // Checks if the current route's name starts with 'admin.'
                return route('admin.login.form'); // Redirect to your specific admin login page
            }

            return route('login'); // For all other unauthenticated requests
        }

        return null;
    }

    
}