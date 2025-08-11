<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Patient;


class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_datetime',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class,'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class,'doctor_id');
    }
}