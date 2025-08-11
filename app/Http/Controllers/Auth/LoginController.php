<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // Make sure this is imported
use Illuminate\Support\Facades\Auth; // Make sure this is imported
use App\Providers\RouteServiceProvider; // Make sure this is imported at the top

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * You can comment this out or remove it, as the authenticated method below will handle redirection.
     * @var string
     */
    // protected $redirectTo = '/patient/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

/**
     * Get the authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // This is the key change!
        // It combines email, password, AND the selected role from the form
        return [
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role, // Add the role from the request
        ];
    }



/**
     * The user has been authenticated.
     * This method is called after a user successfully logs in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user  The authenticated User model instance
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        switch ($user->role) {
            case 'doctor':
                return redirect()->intended(route('doctor.dashboard'));
            case 'caregiver':
                return redirect()->intended(route('caregiver.dashboard'));
            case 'patient':
                return redirect()->intended(route('patient.dashboard'));
            default:
                // Fallback if the user's role is not recognized
                return redirect()->intended(RouteServiceProvider::HOME);
        }
    }


    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/'); // Redirect to the homepage after logout
    }

    
}