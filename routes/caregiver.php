<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Caregiver;
use App\Http\Controllers\Caregiver\MessageController; 
use App\Http\Controllers\Caregiver\AlertController;

/*
|--------------------------------------------------------------------------
| Caregiver Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'check.role:caregiver'])->group(function () {
    Route::get('/dashboard', [Caregiver\DashboardController::class, 'index'])->name('dashboard');
       Route::resource('patients', Caregiver\PatientController::class)->only([
        'index',
        'show'
    ])->names([
        'index' => 'patients.index',
        'show'  => 'patients.show',
    ]);

    Route::get('/tasks', [Caregiver\TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [Caregiver\TaskController::class, 'show'])->name('tasks.show');
Route::patch('/tasks/{task}/complete', [Caregiver\TaskController::class, 'markAsComplete'])->name('tasks.complete');


Route::post('/alerts/{alert}/mark-resolved', [AlertController::class, 'markResolved'])->name('alerts.mark-resolved');
    Route::post('/alerts/mark-all-resolved', [AlertController::class, 'markAllResolved'])->name('alerts.mark-all-resolved');
Route::get('/alerts', [Caregiver\AlertController::class, 'index'])->name('alerts');



Route::get('/messages', [Caregiver\MessageController::class, 'index'])->name('messages.index');
// >>> ADD THIS NEW ROUTE <<<
    // Route to display doctors a caregiver can message
    Route::get('/messages/compose', [MessageController::class, 'composeNew'])->name('messages.compose');

    // Route for a specific conversation (to be implemented later)
    Route::get('/messages/{conversation}', [MessageController::class, 'showConversation'])->name('messages.show');
    // Route for sending a message (to be implemented later)
    Route::post('/messages/{conversation}/send', [MessageController::class, 'sendMessage'])->name('messages.send');
// routes/caregiver.php
// ...
Route::post('/messages/find-or-create-conversation', [MessageController::class, 'findOrCreateConversation'])->name('messages.findOrCreateConversation');
// ...
    
    Route::get('/notifications', [Caregiver\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/profile', [Caregiver\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [Caregiver\ProfileController::class, 'update'])->name('profile.update');

});