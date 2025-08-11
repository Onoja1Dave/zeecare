<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor; 
use App\Http\Controllers\Doctor\AppointmentController;
// Import the Doctor controllers namespace

/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.role:doctor'])->group(function () {
    Route::get('/dashboard', [Doctor\DashboardController::class, 'index'])->name('dashboard');


    Route::get('/patients', [Doctor\PatientController::class, 'index'])->name('patients.index');
    Route::get('doctor/patients/assign', [Doctor\PatientController::class, 'showAssignForm'])->name('assign.form');
    Route::post('/patients/assign', [Doctor\PatientController::class, 'assignPatient'])->name('assign.patient');
    Route::get('/patients/{patient}', [Doctor\PatientController::class, 'show'])->name('patients.show');
    


    // Route to handle the submission of the patient assignment form (ADD/CONFIRM THIS LINE)
    Route::post('/doctor/patients/assign', [Doctor\PatientController::class, 'assignPatient'])->name('assign');
    // --- END PATIENT ASSIGNMENT ROUTES ---

Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index'); // Corrected from 'appointments'
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus'); // Corrected from 'doctor.appointments.updateStatus'
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create'); // Already correct relative to outer prefix
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store'); // Already correct relative to outer prefix
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show'); // Corrected from 'doctor.appointments.show'


Route::get('/prescriptions', [Doctor\PrescriptionController::class, 'index'])->name('prescriptions');
 Route::get('/prescriptions/create/{patient}', [Doctor\PrescriptionController::class, 'create'])->name('prescriptions.create');
Route::post('/prescriptions', [Doctor\PrescriptionController::class, 'store'])->name('prescriptions.store');

  Route::get('/notes', [Doctor\NoteController::class, 'index'])->name('notes.index');
        Route::get('/notes/create/{patient}', [Doctor\NoteController::class, 'create'])->name('notes.create');
        Route::post('/notes', [Doctor\NoteController::class, 'store'])->name('notes.store');

Route::get('/messages', [Doctor\MessageController::class, 'index'])->name('messages.index');
Route::get('/messages/create/{patient?}', [Doctor\MessageController::class, 'create'])->name('messages.create'); // <-- ADD THIS LINE
 Route::get('/messages/with/{patient}', [Doctor\MessageController::class, 'getOrCreateConversation'])->name('messages.getOrCreate');
Route::post('/messages/{conversation}/send', [Doctor\MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/{conversation}', [Doctor\MessageController::class, 'show'])->name('messages.show');
  




    Route::get('/notifications', [Doctor\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/profile', [Doctor\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [Doctor\ProfileController::class, 'update'])->name('profile.update');
});