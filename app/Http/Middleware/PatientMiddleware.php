<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PatientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Inside PatientMiddleware.php, handle method
if (Auth::check()) {
    if (Auth::user()->patientInfo) {
        dd('PatientMiddleware: User is authenticated AND HAS patientInfo. Proceeding.');
    } else {
        dd('PatientMiddleware: User is authenticated BUT DOES NOT HAVE patientInfo. Redirecting to /home.');
        return redirect('/home')->with('error', 'You do not have patient access.');
    }
} else {
    dd('PatientMiddleware: User is NOT authenticated. This should not be hit if auth middleware runs first.');
    return redirect('/home')->with('error', 'You do not have patient access.');
}
    }
}