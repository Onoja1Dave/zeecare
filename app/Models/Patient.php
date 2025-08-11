<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // Ensure this is imported for hasOne
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Ensure this is imported for belongsToMany
use App\Models\Conversation;
use App\Models\User; // Already there, but good to check
use App\Models\MedicationDose;
use App\Models\Appointment;
use App\Models\DoctorNote;
use App\Models\Prescription; // Make sure Prescription model exists and is imported
use App\Models\Note; // Assuming 'Note' model exists for patient notes


class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
       'name',
        'user_id',
        'date_of_birth',
        'gender',
        'contact_number',
        'medical_history',
        'assigned_doctor_id',
        'caregiver_id', // Make sure this is in your fillable if you added the column via migration
        'condition',    // Make sure this is in your fillable if you added the column
        'notes',        // Make sure this is in your fillable if you added the column
    ];

    /**
     * The User record associated with this Patient (if the patient also has a login account).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The primary doctor assigned to this patient (via assigned_doctor_id column).
     */
    public function assignedDoctor(): BelongsTo
    {
        // Renamed from 'doctor' to 'assignedDoctor' to avoid confusion with the many-to-many 'doctors()'
        return $this->belongsTo(User::class, 'assigned_doctor_id');
    }

    /**
     * The primary caregiver assigned to this patient (via caregiver_id column).
     * This corresponds to the 'caregiver_id' column we added to your patients table.
     */
    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }

    /**
     * All prescriptions for this patient.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * All appointments for this patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class,'patient_id');
    }

    /**
     * All general notes related to this patient.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * All assigned caregivers through the pivot table (many-to-many).
     * Assuming 'caregiver_patient_assignments' is your pivot table.
     */
    public function caregivers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'caregiver_patient_assignments', 'patient_id', 'caregiver_id');
    }

    /**
     * All assigned doctors through the pivot table (many-to-many).
     * Assuming 'doctor_patient_assignments' is your pivot table.
     */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'doctor_patient_assignments', 'patient_id', 'doctor_id');
    }

    /**
     * The main conversation associated with this patient.
     * Assuming Conversation model exists and has 'patient_id' foreign key.
     */
  public function conversations(): HasMany 
{
    return $this->hasMany(Conversation::class, 'patient_id');
}

    /**
     * All medication doses for this patient.
     */
    public function medicationDoses(): HasMany
    {
        return $this->hasMany(MedicationDose::class, 'patient_id');
    }
    /**
     * A patient can have many doctor's notes.
     * Order by newest first so they appear chronologically.
     * Assuming DoctorNote model exists and has 'patient_id' foreign key.
     */
    public function doctorNotes(): HasMany
    {
        return $this->hasMany(DoctorNote::class, 'patient_id')->orderByDesc('created_at');
    }

    /**
     * A patient can have many alerts related to them.
     * Assuming Alert model exists and has 'patient_id' foreign key.
     */
    public function alerts(): HasMany // NEW: Added this relationship for the dashboard
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * A patient can have many tasks.
     * Assuming Task model exists and has 'patient_id' foreign key.
     */
    public function tasks(): HasMany // NEW: Added this relationship for the dashboard
    {
        return $this->hasMany(Task::class);
    }
}