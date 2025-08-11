<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Caregiver; // Re-add this import!
use App\Models\Patient; // Keep this if you have a separate Patient model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of all users for admin management.
     * Corresponds to admin.users.index
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Re-add with('caregiver') for eager loading
        //$users = User::with(['caregiver'])
               $users = User::orderBy('created_at', 'desc')
                     ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     * Corresponds to admin.users.create (optional for now)
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     * Corresponds to admin.users.store (optional for now)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => ['required', 'string', Rule::in(['patient', 'doctor', 'caregiver', 'admin'])],
            // Doctor status on User model
            'status' => Rule::requiredIf($request->user_type == 'doctor') . '|in:pending,approved,rejected',
            // Caregiver status on Caregiver model
            'caregiver_status' => Rule::requiredIf($request->user_type == 'caregiver') . '|in:pending,approved,rejected',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'status' => ($request->user_type === 'doctor') ? $request->status : 'approved', // Status for Doctors or default for others
        ]);

        if ($user->isCaregiver()) {
            Caregiver::create(['user_id' => $user->id, 'status' => $request->caregiver_status ?? 'pending']); // Default pending for new caregivers
        } elseif ($user->isPatient()) {
            Patient::create(['user_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     * Corresponds to admin.users.edit
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Eager load caregiver relationship if it exists for the user
        $user->loadMissing('caregiver');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     * Corresponds to admin.users.update
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'user_type' => ['required', 'string', Rule::in(['patient', 'doctor', 'caregiver', 'admin'])],
            'password' => 'nullable|string|min:8|confirmed',


// Doctor status on User model
            'status' => Rule::requiredIf($request->user_type == 'doctor') . '|in:pending,approved,rejected',
            // Caregiver status on Caregiver model
            'caregiver_status' => Rule::requiredIf($request->user_type == 'caregiver') . '|in:pending,approved,rejected',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_type = $request->user_type;

        // Update Doctor status directly on the User model
        if ($user->isDoctor()) {
            $user->status = $request->status;
        } else {
            // For other roles, status might not be applicable or always 'approved'
            // Consider what happens if user type changes from doctor to something else
            $user->status = 'approved'; // Default, or null
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Handle Caregiver profile creation/update/deletion
        if ($user->isCaregiver()) {
            $user->caregiver()->updateOrCreate(
                ['user_id' => $user->id],
                ['status' => $request->caregiver_status ?? 'pending'] // Provide a default if not set
            );
        } else {
            // If user type changed from caregiver, delete the caregiver profile
            if ($user->caregiver) {
                $user->caregiver->delete();
            }
        }

        // Handle Patient profile creation/deletion if you have a separate Patient model
        if ($user->isPatient()) {
            $user->patient()->updateOrCreate(
                ['user_id' => $user->id], []
            );
        } else {
            if ($user->patient) {
                $user->patient->delete();
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }


    /**
     * Remove the specified user from storage.
     * Corresponds to admin.users.destroy
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Prevent deleting the currently authenticated admin user
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        // Delete associated caregiver profile if it exists
        if ($user->isCaregiver() && $user->caregiver) {
            $user->caregiver->delete();
        }

        // Delete associated patient profile if it exists
        if ($user->isPatient() && $user->patient) {
            $user->patient->delete();
        }

        $user->delete(); // This will delete the user from the 'users' table

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}