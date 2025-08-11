<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group and 'admin' prefix/name.
|
*/

// All routes in this file will automatically have the '/admin' prefix
// and 'admin.' name prefix, and will use 'web' middleware.

// --- ADMIN AUTHENTICATION ROUTES (PUBLICLY ACCESSIBLE) ---
// These routes must be OUTSIDE of any 'auth' middleware
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
// --- END ADMIN AUTHENTICATION ROUTES ---


// --- PROTECTED ADMIN ROUTES (Require Authentication) ---
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin Dashboard (already exists, but now in the dedicated file)
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Manage Users
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    // Add other user management routes (create, store, show, edit, update, destroy) here if needed
    Route::resource('users', UserController::class);

    // Pending Approvals
    Route::get('/pending-approvals', [Admin\PendingApprovalController::class, 'index'])->name('pending-approvals');
    Route::post('/pending-approvals/{user}/approve', [Admin\PendingApprovalController::class, 'approve'])->name('pending-approvals.approve');
    Route::post('/pending-approvals/{user}/reject', [Admin\PendingApprovalController::class, 'reject'])->name('pending-approvals.reject');

    // Settings (example)
    Route::get('/settings', [Admin\SettingsController::class, 'index'])->name('settings');

    // Admin Profile (example)
    Route::get('/profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [Admin\ProfileController::class, 'update'])->name('profile.update');

    // Caregiver Application Management (from your previous route block)
    Route::get('/caregiver-applications', [Admin\CaregiverApplicationController::class, 'index'])->name('caregiver_applications.index');
    Route::post('/caregiver-applications/{caregiverApplication}/approve', [Admin\CaregiverApplicationController::class, 'approve'])->name('caregiver_applications.approve');
    Route::post('/caregiver-applications/{caregiverApplication}/reject', [Admin\CaregiverApplicationController::class, 'reject'])->name('caregiver_applications.reject');
});