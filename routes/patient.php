<?php

use Illuminate\Support\Facades\Route;
// Import all patient-specific controllers explicitly
use App\Http\Controllers\Patient\DashboardController;
use App\Http\Controllers\Patient\ProfileController;
use App\Http\Controllers\Patient\AppointmentController;
use App\Http\Controllers\Patient\PrescriptionController; // Assuming you use this
use App\Http\Controllers\Patient\NoteController;         // Assuming you use this
use App\Http\Controllers\Patient\NotificationController;
use App\Http\Controllers\Patient\MedicationDoseController;
use App\Http\Controllers\Patient\MessageController;


/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
|
| These routes are for the patient-specific section of the application.
| They are protected by 'auth' and 'check.role:patient' middleware,
| and are prefixed with 'patient.' for easy naming.
|
*/

Route::middleware(['auth', 'check.role:patient'])->name('patient.')->group(function () {

    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Appointment Routes
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    // Add more appointment routes as needed (e.g., create, show, store, update, delete)
     Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
     Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
     Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');


    // Medication Routes
Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::post('/medication-doses/{medicationDose}/mark-taken', [MedicationDoseController::class, 'markTaken'])->name('medication-doses.mark-taken');


    // Message Routes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/{conversation}', [MessageController::class, 'show'])->name('show'); // View specific conversation
        Route::post('/{conversation}/send', [MessageController::class, 'send'])->name('send'); // Send message in conversation
        Route::get('/doctor', [MessageController::class, 'startConversationWithPrescribingDoctor'])->name('start_with_doctor'); // Start new chat with doctor
    });


    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
Route::get('/notes/{doctorNote}', [NoteController::class, 'show'])->name('notes.show');
Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    // Other Routes from your original file (if needed)
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index'); // Renamed for consistency

});