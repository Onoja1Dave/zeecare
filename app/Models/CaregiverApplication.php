<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaregiverApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email', 
        'contact_info', 
        'reason', 
        'experience', 
        'status',
        'admin_notes',
        'registration_token', 
        'token_expires_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];
}