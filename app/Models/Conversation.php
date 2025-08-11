<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'caregiver_id',
    ];

    /**
     * Get the patient associated with the conversation.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the doctor (User) associated with the conversation.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

   
    public function caregiver()
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }
    
    public function messages()
    {
        return $this->hasMany(Message::class)->oldest(); // Use oldest() for conversation flow
    }

    /**
     * Get the latest message in the conversation.
     * Use latestOfMany() to efficiently retrieve only the latest message.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get the count of unread messages for a specific user in this conversation.
     *
     * @param int $userId The ID of the user whose unread messages to count.
     * @return int
     */
    public function unreadMessagesCountForUser(int $userId): int
    {
        return $this->messages()
                    ->whereNull('read_at')     // Message has not been read
                    ->where('sender_id', '!=', $userId) // Message was sent by the other participant
                    ->count();
    }
}