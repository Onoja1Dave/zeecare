<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\MedicationDose;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrescriptionController extends Controller
{
    /**
     * Show the form for creating a new prescription.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $patients = \App\Models\Patient::all();
        $doctors = \App\Models\User::where('role', 'doctor')->get();

        return view('prescriptions.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created prescription in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'drug_name' => 'required|max:255',
            'dosage' => 'required|max:255',
            'frequency' => 'required|max:255',
            'duration' => 'nullable|max:255',
        ]);

        $prescription = Prescription::create($validatedData);

        // Logic to generate medication doses will go here
        $this->generateMedicationDoses($prescription);

        return redirect()->route('home')->with('success', 'Prescription saved successfully!');
    }

   protected function generateMedicationDoses(Prescription $prescription)
{
    $frequency = $prescription->frequency;
    $durationText = $prescription->duration;
    $patientId = $prescription->patient_id;
    $prescriptionId = $prescription->id;
    $startDate = Carbon::now();
    $endDate = null;

    // Parse the duration to calculate the end date
    if ($durationText) {
        if (strpos($durationText, 'day') !== false) {
            $days = (int) filter_var($durationText, FILTER_SANITIZE_NUMBER_INT);
            $endDate = $startDate->copy()->addDays($days);
        } elseif (strpos($durationText, 'week') !== false) {
            $weeks = (int) filter_var($durationText, FILTER_SANITIZE_NUMBER_INT);
            $endDate = $startDate->copy()->addWeeks($weeks);
        }
        // Add more duration parsing logic if needed (e.g., months)
    } else {
        // If no duration is provided, we might schedule for a default period or indefinitely
        // For now, let's just schedule for the current day if no duration is given.
        $endDate = $startDate->copy()->endOfDay();
    }

    $dosesPerDay = 0;
    if (stripos($frequency, 'once') !== false) {
        $dosesPerDay = 1;
    } elseif (stripos($frequency, 'twice') !== false) {
        $dosesPerDay = 2;
    } elseif (preg_match('/(\d+) times per day/i', $frequency, $matches)) {
        $dosesPerDay = (int) $matches[1];
    }

    if ($dosesPerDay > 0) {
        $currentDate = $startDate->copy()->startOfDay();
        while ($currentDate <= $endDate) {
            // Determine the scheduled times for each dose on the current day
            for ($i = 0; $i < $dosesPerDay; $i++) {
                // For simplicity, let's space them evenly throughout the day
                $hour = 8 + floor((16 * $i) / $dosesPerDay); // Spread between 8 AM and 12 AM
                $minute = 0;

                $scheduledAt = $currentDate->copy()->hour($hour)->minute($minute)->second(0);

                MedicationDose::create([
                    'prescription_id' => $prescriptionId,
                    'patient_id' => $patientId,
                    'scheduled_at' => $scheduledAt,
                ]);
            }
            $currentDate->addDay();
        }
    }
}
}