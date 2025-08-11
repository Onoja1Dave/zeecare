<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Prescription;



class MedicationDose extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'patient_id',
        'scheduled_at',
        'taken_at',
        'status', 
        'is_taken', 
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'taken_at' => 'datetime',
        'is_taken' => 'boolean', // <-- Crucial: Cast to boolean
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class,'prescription_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}