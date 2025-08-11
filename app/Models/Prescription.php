<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Patient;
use App\Models\MedicationDose; 
use Carbon\Carbon;


class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'drug_name',
        'dosage',
        'frequency',
        'duration',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class,'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class,'doctor_id');
    }

public function medicationDoses()
{
    return $this->hasMany(MedicationDose::class);
}

}