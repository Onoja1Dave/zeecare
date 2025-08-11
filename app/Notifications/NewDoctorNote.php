<?php

namespace App\Notifications;

use App\Models\DoctorNote; // Make sure to import your DoctorNote model
use App\Models\Patient;   // Make sure to import your Patient model
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDoctorNote extends Notification implements ShouldQueue // Added ShouldQueue for better performance
{
    use Queueable;

    protected $doctorNote;
    protected $patient;

    /**
     * Create a new notification instance.
     */
    public function __construct(DoctorNote $doctorNote, Patient $patient)
    {
        $this->doctorNote = $doctorNote;
        $this->patient = $patient;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // We'll start with 'database' for in-app notifications.
        // You can add 'mail' or 'vonage' (for SMS) later if configured.
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'note_id' => $this->doctorNote->id,
            'patient_id' => $this->patient->id,
            'patient_name' => $this->patient->user->name, // Assuming Patient model has a user relationship
            'doctor_id' => $this->doctorNote->doctor_id,
            'doctor_name' => $this->doctorNote->doctor->name, // Assuming DoctorNote has a doctor relationship
            'note_content_snippet' => substr($this->doctorNote->content, 0, 100) . (strlen($this->doctorNote->content) > 100 ? '...' : ''),
            'type' => 'doctor_note_added',
            'link' => route('patient.notes.show', $this->doctorNote->id), // Example: Link to view the note on patient side
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Optional: Mail Channel (Uncomment and customize if you want email notifications)
    |--------------------------------------------------------------------------
    */
    // /**
    //  * Get the mail representation of the notification.
    //  */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('A new note has been added for you by Dr. ' . $this->doctorNote->doctor->name . '.')
    //                 ->action('View Note', url($this->doctorNote->link))
    //                 ->line('Thank you for using CareLink!');
    // }
}