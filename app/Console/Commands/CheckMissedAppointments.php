<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log; // For logging if needed

class CheckMissedAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:check-missed-appointments'; // The command you'll run

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for missed appointments and generates urgent alerts for caregivers.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting check for missed appointments...');

        // Get all appointments that were scheduled for before now
        // and whose status is NOT 'completed' or 'cancelled'.
        // This indicates a potentially missed appointment.
        $missedAppointments = Appointment::where('appointment_datetime', '<', Carbon::now())
                                         ->whereNotIn('status', ['completed', 'cancelled'])
                                         ->with('patient.caregiver') // Eager load patient and its caregiver
                                         ->get();

        $generatedAlertsCount = 0;

        foreach ($missedAppointments as $appointment) {
            // Ensure the appointment has a patient and the patient has a caregiver
            if ($appointment->patient && $appointment->patient->caregiver) {
                $caregiverId = $appointment->patient->caregiver->id;
                $patientId = $appointment->patient->id;

                // Check if an alert for this specific missed appointment already exists
                // This prevents duplicate alerts for the same missed event
                $existingAlert = Alert::where('patient_id', $patientId)
                                      ->where('caregiver_id', $caregiverId)
                                      ->where('type', 'missed_appointment')
                                      ->where('message', 'LIKE', '%Appointment ID: ' . $appointment->id . '%') // Use ID for unique message
                                      ->where('is_resolved', false) // Only care about unresolved duplicates
                                      ->first();

                if (!$existingAlert) {
                    // Create a new Alert
                    Alert::create([
                        'patient_id'   => $patientId,
                        'caregiver_id' => $caregiverId,
                        'type'         => 'missed_appointment',
                        'message'      => "Patient " . $appointment->patient->user->name . " missed an appointment scheduled for " . $appointment->appointment_datetime->format('M d, Y h:i A') . ". (Appointment ID: {$appointment->id})",
                        'is_resolved'  => false,
                        'resolved_at'  => null,
                    ]);
                    $generatedAlertsCount++;
                }
            } else {
                Log::warning("Missed appointment (ID: {$appointment->id}) could not generate alert: Missing patient or caregiver association.");
            }
        }

        $this->info("Finished checking. Generated {$generatedAlertsCount} new missed appointment alerts.");
        return Command::SUCCESS;
    }
}