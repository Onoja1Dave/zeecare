<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log; // <--- ADD THIS LINE at the top!

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // --- ADD ALL OF THESE LINES BELOW THIS COMMENT ---

        Log::info('AdminMiddleware: Checking authentication and role.');
        Log::info('Auth::check(): ' . (Auth::check() ? 'true' : 'false'));

        if (Auth::check()) {
            $user = Auth::user();
            Log::info('Authenticated User ID: ' . $user->id);
            Log::info('Authenticated User Role (from DB): ' . $user->role);
            Log::info('Is user role "admin"? ' . ($user->role === 'admin' ? 'true' : 'false'));

            if ($user->role === 'admin') {
                Log::info('AdminMiddleware: User is admin. Proceeding.');
                return $next($request); // User is an admin, proceed with the request
            } else {
                Log::warning('AdminMiddleware: User is authenticated but role is NOT "admin". Role: ' . $user->role);
            }
        } else {
            Log::warning('AdminMiddleware: User is NOT authenticated.');
        }

        // --- AND MAKE SURE THE LINES ABOVE THIS COMMENT ARE ADDED ---


        // If not an admin, redirect them or abort the request
        return redirect('/')->with('error', 'You do not have administrative access.');
        // Or to a 403 Forbidden page: abort(403, 'Unauthorized access.');
    }
}