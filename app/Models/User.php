<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Conversation; 
use App\Models\Message;
use App\Models\Caregiver;  
use App\Models\Patient;  
use App\Models\DoctorNote;
use Carbon\Carbon;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_type', 
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function scopeAdmin($query)
{
    return $query->where('role', 'admin');
}

public function scopeDoctor($query)
{
    return $query->where('role', 'doctor');
}

public function scopePatient($query)
{
    return $query->where('role', 'patient');
}

public function scopeCaregiver($query)
{
    return $query->where('role', 'caregiver');
}

public function scopeApproved($query)
{
    return $query->whereNotNull('approved_at');
}

public function scopePendingApproval($query)
{
    return $query->whereNull('approved_at')->whereIn('role', ['doctor', 'caregiver']);
}

// Relationships for Doctors
    public function doctorPrescriptions()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function doctorNotes()
    {
        return $this->hasMany(DoctorNote::class, 'doctor_id');
    }

    public function assignedPatientsAsDoctor()
    {
        return $this->belongsToMany(Patient::class, 'doctor_patient_assignments', 'doctor_id', 'patient_id');
    }

    // Relationships for Patients (can be accessed via a User model with 'patient' role)
    public function patientInfo()
    {
        return $this->hasOne(Patient::class);
    }

    public function patientPrescriptions()
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

public function patientAppointments(): HasMany
    {
        // First, check if this user actually has a linked patient profile.
        // Also check if that patient profile has an assigned doctor.
        if (!$this->patientProfile || !$this->patientProfile->assigned_doctor_id) {
            // If no patient profile or no assigned doctor, return an empty query.
            return $this->hasMany(Appointment::class, 'patient_id')->whereRaw('1 = 0');
        }

        // CORRECTED: Removed the redundant ->where('patient_id', ...)
        // The hasMany relationship already correctly links 'appointments.patient_id' to 'users.id'
        return $this->hasMany(Appointment::class, 'patient_id', 'id')
                    ->where('doctor_id', $this->patientProfile->assigned_doctor_id)
                    ->where('appointment_datetime', '>=', Carbon::now());
    }


    public function patientNotes()
    {
        return $this->hasMany(Note::class, 'patient_id');
    }

    public function assignedCaregivers()
    {
        return $this->belongsToMany(User::class, 'caregiver_patient_assignments', 'patient_id', 'caregiver_id');
    }

    public function assignedDoctors()
    {
        return $this->belongsToMany(User::class, 'doctor_patient_assignments', 'patient_id', 'doctor_id');
    }

    // Relationships for Caregivers
    public function caregiverInfo()
    {
        return $this->hasOne(Caregiver::class);
    }

    public function assignedPatientsAsCaregiver()
    {
        return $this->belongsToMany(Patient::class, 'caregiver_patient_assignments', 'caregiver_id', 'patient_id');
    }

    public function doctorConversations()
    {
        return $this->hasMany(Conversation::class, 'doctor_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * A doctor (User) can have many patients assigned to them.
     */
    public function patients()
    {
        // 'assigned_doctor_id' is the foreign key on the 'patients' table
        return $this->hasMany(Patient::class, 'assigned_doctor_id');
    }

    /**
     * Get conversations where this user is the doctor.
     */
    public function conversationsAsDoctor()
    {
        return $this->hasMany(Conversation::class, 'doctor_id');
    }

    /**
     * Get all messages sent by this user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
  
/**
     * A user with 'caregiver' role has many patients under their care.
     */
    public function patientsUnderCare(): HasMany
    {
        return $this->hasMany(Patient::class, 'caregiver_id');
    }

    /**
     * A user with 'doctor' role has many patients assigned to them.
     */
    public function patientsAssigned(): HasMany
    {
        return $this->hasMany(Patient::class, 'assigned_doctor_id');
    }

    /**
     * A user with 'doctor' role has many prescriptions.
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    /**
     * A user with 'caregiver' role has many tasks assigned to them.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'caregiver_id');
    }

    /**
     * A user (caregiver or doctor) might be involved in many appointments.
     */
    public function appointmentsInvolved(): HasMany
    {
        // Assuming a user can be either a caregiver or a doctor in an appointment
        // This might need refinement if you want to distinguish specific roles
        return $this->hasMany(Appointment::class, 'caregiver_id')->orWhere('doctor_id', $this->id);
    }
     // Note: The above appointmentsInvolved is a common pattern but can be tricky
     // for orWhere within a single relationship definition.
     // A more robust way might be separate relationships if strict role distinction needed.
     // For now, let's keep it simple for illustration.

    /**
     * A user with 'patient' role might have many alerts related to them.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'caregiver_id'); // If caregiver is the primary recipient of the alert
        // Or if Patient is a User: return $this->hasMany(Alert::class, 'patient_id');
    }

    // A patient is a user, so the patient's own record in the 'patients' table
    public function patientRecord(): HasOne
    {
        return $this->hasOne(Patient::class, 'user_id');
    }


    public function patientProfile()
    {
        return $this->hasOne(Patient::class, 'user_id'); // Assumes 'patients' table has 'user_id' column
    }

    // Helper methods to quickly check role
    public function isPatient()
    {
        return $this->role === 'patient';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

public function isCaregiver()
    {
        return $this->role === 'caregiver';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }


/**
     * Get the total count of unread messages for the user.
     * This method will handle both doctor and caregiver roles.
     *
     * @return int
     */
    public function getTotalUnreadMessagesCount(): int
    {
        $userId = $this->id;
        $unreadCount = 0;

        // Fetch conversations where this user is either the doctor or the caregiver
        $conversations = \App\Models\Conversation::where('doctor_id', $userId)
                                     ->orWhere('caregiver_id', $userId)
                                     ->get();

        foreach ($conversations as $conversation) {
            // Sum unread messages in each conversation where the sender is not the current user
            $unreadCount += $conversation->messages()
                                         ->where('sender_id', '!=', $userId)
                                         ->whereNull('read_at')
                                         ->count();
        }

        return $unreadCount;
    }





}
