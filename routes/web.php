<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Patient\MedicationDoseController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController; 
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;


use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController; // Keep for dedicated Admin login

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// -------------------------------------------------------------------
// 1. General & Guest Routes (Accessible to anyone)
// -------------------------------------------------------------------

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes (Managed by Laravel's Auth scaffolding for general users)
Auth::routes([
    'register' => true, // Enable registration
    'reset' => false,   // Disable password reset (as per your previous setup)
    'verify' => false   // Disable email verification (as per your previous setup)
]);


// Caregiver Application Routes (Public - these are for applying, not the panel itself)
Route::get('/apply-as-caregiver', [App\Http\Controllers\CaregiverApplicationController::class, 'showApplicationForm'])->name('caregiver.apply.form');
Route::post('/apply-as-caregiver', [App\Http\Controllers\CaregiverApplicationController::class, 'storeApplication'])->name('caregiver.apply.store');


// Caregiver Token-Based Registration (Public - part of the registration flow)
Route::get('/register/caregiver/{token}', [App\Http\Controllers\Auth\RegisterController::class, 'showCaregiverRegistrationForm'])->name('register.caregiver');
Route::post('/register/caregiver', [App\Http\Controllers\Auth\RegisterController::class, 'caregiverRegister'])->name('register.caregiver.post');




// Generic /dashboard route (This should ideally redirect based on user role)
// This is typically handled by your LoginController's redirect logic or a middleware.
// If you have a generic dashboard for unroled users, you can keep it, otherwise
// ensure your LoginController redirects authenticated users to their specific role dashboards.
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        } elseif (Auth::user()->role === 'caregiver') {
            return redirect()->route('caregiver.dashboard');
        } elseif (Auth::user()->role === 'patient') {
            return redirect()->route('patient.dashboard');
        }
        return view('dashboard'); // Fallback for users without a specific role or during development
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/patient/medication-doses/{medicationDose}/mark-taken', [MedicationDoseController::class, 'markTaken']) // <--- CORRECTED LINE
        ->name('patient.medication-doses.mark-taken');
});



