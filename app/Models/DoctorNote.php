<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorNote extends Model
{
    use HasFactory;

    protected $table = 'notes';
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'content',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class,'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class,'doctor_id');
    }
}