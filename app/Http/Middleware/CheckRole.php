<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            // If not authenticated, redirect to login page
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if the user has any of the required roles
        if (!in_array($user->role, $roles)) {
            // If user does not have the required role, abort with 403 Forbidden
            // Or redirect them to a specific page with an error message
            return redirect('/home')->with('error', 'You do not have permission to access this page.');
            // You could also use: abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}