<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\CaregiverApplication; // Import CaregiverApplication model
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // <--- Add this line
use Illuminate\Http\Request; // Import Request for the new methods
use Carbon\Carbon; // Import Carbon for checking token expiry


class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home'; // This will be overridden later for role-based redirect, as previously set up

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Apply guest middleware to default register route, but exclude caregiver-specific routes
        $this->middleware('guest')->except(['showCaregiverRegistrationForm', 'caregiverRegister']);
    }

    /**
     * Get a validator for an incoming registration request.
     * (This is for the standard public registration form)
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_patient' => ['nullable', 'boolean'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'contact_number' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * (This is for the standard public registration form)
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
   $initialRole = (isset($data['is_patient']) && $data['is_patient']) ? 'patient' : null;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $initialRole,
           
        ]);

       // 2. Create the Patient record in the 'patients' table and link it to the newly created User
       if (isset($data['is_patient']) && $data['is_patient']) {
       Patient::create([
            'user_id' => $user->id, // Link to the newly created user using their ID
            'name' => $data['name'], // Also store the name in the patients table
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'medical_history' => null, // Set to null as it's not from the registration form
            'assigned_doctor_id' => null, // Set to null as it's not from the registration form
        ]);
    }
    
        return $user;
    }

    /**
     * Show the caregiver registration form with token validation.
     *
     * @param string $token
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showCaregiverRegistrationForm($token)
    {
        $application = CaregiverApplication::where('registration_token', $token)
                                        ->where('status', 'approved')
                                        ->where('token_expires_at', '>', Carbon::now())
                                        ->first();

        if (!$application) {
            // Token is invalid, expired, or application not approved
            return redirect()->route('login')->with('error', 'Invalid or expired registration link. Please contact support.');
        }

        // If the email from the application is already a registered user,
        // prevent them from registering again with this link
        if (User::where('email', $application->email)->exists()) {
            return redirect()->route('login')->with('error', 'This email is already registered. Please log in.');
        }

        return view('auth.caregiver_register', compact('application'));
    }

    /**
     * Handle a caregiver registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function caregiverRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string', 'exists:caregiver_applications,registration_token'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $application = CaregiverApplication::where('registration_token', $request->token)
                                        ->where('status', 'approved')
                                        ->where('token_expires_at', '>', Carbon::now())
                                        ->first();

        if (!$application) {
            return redirect()->route('login')->with('error', 'Invalid or expired registration link. Please contact support.');
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $application->email, // Use email from the approved application
            'password' => Hash::make($request->password),
            'role' => 'caregiver', // Automatically set role to caregiver
        ]);

        // Invalidate the token after use to prevent reuse
        $application->registration_token = null;
        $application->token_expires_at = null;
        $application->save();

        // Log the user in after registration
        $this->guard()->login($user);

        return $this->registered($request, $user)
                    ?: redirect($this->redirectPath()); // Redirect to caregiver dashboard
    }

/**
 * Get the redirect path for the user after registration based on their role.
 *
 * @return string
 */
protected function redirectPath()
{
    if (auth()->user()->role === 'admin') {
        return '/admin/dashboard';
    } elseif (auth()->user()->role === 'doctor') {
        return '/doctor/dashboard';
    } elseif (auth()->user()->role === 'caregiver') {
        return '/caregiver/dashboard'; // <--- This is the target for caregivers
    } elseif (auth()->user()->role === 'patient') {
        return '/patient/dashboard';
    }

    // Fallback if role is not recognized or defined
    return '/home';
}

}