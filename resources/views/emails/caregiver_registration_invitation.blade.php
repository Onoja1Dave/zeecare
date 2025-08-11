{{-- resources/views/emails/caregiver_registration_invitation.blade.php --}}

@component('mail::message')
# Caregiver Registration Invitation

Hello {{ $application->name }},

Congratulations! Your application to become a professional caregiver at CareLink has been approved.

To complete your registration and set up your account, please click the button below:

@component('mail::button', ['url' => route('register.caregiver', ['token' => $application->registration_token])])
Complete Registration
@endcomponent

This registration link is valid for 24 hours. If the link expires, please contact our administration team to request a new one.

We look forward to having you on board to help our patients!

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent