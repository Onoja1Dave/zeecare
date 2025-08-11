<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caregiver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'experience',
        'status',
        'reason_for_application',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patientAssignments()
    {
        return $this->hasMany(CaregiverPatientAssignment::class, 'caregiver_id');
    }

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'caregiver_patient_assignments', 'caregiver_id', 'patient_id');
    }

}