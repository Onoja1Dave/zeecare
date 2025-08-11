<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicationDose;
use Carbon\Carbon;

class MedicationDoseController extends Controller
{
    public function markTaken(Request $request, MedicationDose $medicationDose)
    {
       dd('SUCCESS! Reached the correct markTaken controller method for dose ID: ' . $medicationDose->id); 
        dd([
            'Step' => 'Before Validation and Update',
            'Request All' => $request->all(),
            'MedicationDose ID Loaded' => $medicationDose->id,
            'Initial MedicationDose Data' => $medicationDose->toArray(),
            'Is Request Validated' => $request->validate([ 'is_taken' => 'required|boolean' ]), // This will run validation, if it fails, it will throw an exception
        ]);

        // If the above dd() passes, remove it and uncomment the validation below
        // $request->validate([
        //     'is_taken' => 'required|boolean',
        // ]);

        $medicationDose->is_taken = $request->input('is_taken');
        $medicationDose->taken_at = $request->input('is_taken') ? now() : null;
        $medicationDose->status = $request->input('is_taken') ? 'taken' : 'scheduled';

        // --- DD POINT 2: Check model state after assignments, but before save ---
        // This will show if the changes are correctly assigned to the model instance.
        dd([
            'Step' => 'After Assignments, Before Save',
            'Updated MedicationDose Data' => $medicationDose->toArray(),
            'Has Model Changed (isDirty)?' => $medicationDose->isDirty(), // Should be true if values changed
            'Which Attributes Changed (getDirty)?' => $medicationDose->getDirty(), // Shows what Laravel thinks changed
        ]);

        $medicationDose->save(); // This is the line that writes to the database

        return response()->json(['success' => true, 'message' => 'Medication dose status updated successfully.']);
    }
}