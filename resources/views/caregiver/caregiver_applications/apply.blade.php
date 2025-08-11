{{-- resources/views/caregiver_applications/apply.blade.php --}}

@extends('layouts.form_only')

@section('content')
<style>
    /* Custom Color Variables & Global Styles (consistent with previous designs) */
    :root {
        --primary-blue: #47B6ED;       /* Vibrant Light Blue */
        --secondary-blue: #2D9CDB;     /* Deeper Blue */
        --background-light: #F9FAFC;   /* Very light background */
        --white: #FFFFFF;
        --text-dark: #333333;
        --text-muted: #666666;
        --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.08); /* Soft, diffused shadow */
        --input-border: #E0E0E0;      /* Light gray border for inputs */
        --input-focus: #C4E3F3;       /* Lighter blue for input focus */
        --form-bg-light-blue: #EBF8FF; /* Light blue for the form card background */
        --border-radius-lg: 15px;
        --border-radius-md: 8px;
    }

    body {
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        background-color: var(--background-light);
        line-height: 1.7;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text-dark);
        font-weight: 700;
    }

    a {
        color: var(--primary-blue);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    a:hover {
        color: var(--secondary-blue);
        text-decoration: underline;
    }

    /* Main container for the two-column layout */
    .caregiver-apply-section {
        display: flex;
        justify-content: center;
        align-items: flex-start; /* Align items to the top */
        padding: 60px 40px; /* Ample padding around the section */
        gap: 60px; /* Space between columns */
        max-width: 1200px; /* Max width of the content area */
        margin: 0 auto; /* Center the content area */
        min-height: 80vh; /* Ensure it takes up most of the viewport height */
        box-sizing: border-box; /* Include padding in element's total width and height */

        
         background-image: url('{{ asset('images/apply3.png') }}'); */
         background-size: cover; */
         background-position: center; */
         background-repeat: no-repeat; */
         background-color: rgba(255, 255, 255, 0.8); /* Optional: add a white overlay for readability */
         background-blend-mode: overlay; */
    }

    /* Left column for information */
    .caregiver-info-column {
        flex: 1.5; /* Takes more space than the form column */
        max-width: 550px; /* Max width for info column content */
        text-align: left;
        padding-top: 20px; /* Adjust top padding to align with form title */
    }

    .caregiver-info-column h2 {
        font-size: 2.5rem;
        margin-bottom: 25px;
        line-height: 1.2;
    }

    /* Styles for the new "Why Choose Us" section */
    .caregiver-benefits-list {
        list-style: none; /* Remove default bullet points */
        padding: 0;
        margin-top: 30px;
    }

    .caregiver-benefits-list li {
        margin-bottom: 15px;
        font-size: 1rem;
        color: var(--text-dark);
        position: relative;
        padding-left: 25px; /* Space for custom bullet */
    }

    .caregiver-benefits-list li::before {
        content: '\f058'; /* Font Awesome check-circle icon */
        font-family: 'Font Awesome 5 Free';
        font-weight: 900; /* Solid icon */
        color: var(--primary-blue);
        position: absolute;
        left: 0;
        top: 0;
        font-size: 1.1rem;
    }

    /* Styles for the "Application Process" section */
    .application-process-steps {
        list-style: none; /* Remove default numbering */
        padding: 0;
        margin-top: 30px;
    }
    .application-process-steps li {
        margin-bottom: 15px;
        font-size: 1rem;
        color: var(--text-dark);
        position: relative;
        padding-left: 30px; /* Space for custom number */
    }

    .application-process-steps li:before {
        content: counter(step-counter); /* Custom counter */
        counter-increment: step-counter;
        background-color: var(--primary-blue);
        color: var(--white);
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        left: 0;
        top: 0;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .caregiver-commitment-text {
        font-size: 0.95rem;
        color: var(--text-muted);
        margin-top: 40px;
        line-height: 1.6;
    }

    /* Right column for the form */
    .caregiver-form-column {
        flex: 1; /* Takes less space than the info column */
        max-width: 450px; /* Max width for form column content */
        background-color: var(--form-bg-light-blue);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-soft);
        padding: 40px;
        box-sizing: border-box;
    }

    .caregiver-form-column h2 {
        font-size: 1.8rem;
        margin-bottom: 30px;
        color: var(--text-dark);
        text-align: left;
    }

    /* Form specific styles */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-dark);
        font-size: 0.95rem;
    }

    .form-control-custom { /* Custom class for inputs/textareas to match design */
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--input-border);
        border-radius: var(--border-radius-md);
        font-size: 1rem;
        color: var(--text-dark);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        box-sizing: border-box; /* Crucial for width calculation */
    }

    .form-control-custom:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px var(--input-focus);
    }

    .form-text {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 5px;
    }

    /* Half-width input group (we're removing this section for full name) */
    /* .form-row-halves {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row-halves .form-group-half {
        flex: 1;
    } */

    .invalid-feedback {
        display: block; /* Make sure it's visible */
        color: #dc3545; /* Red color for errors */
        font-size: 0.85rem;
        margin-top: 5px;
    }

    /* Button styling (matching the "Send An Email" design) */
    .btn-submit-app {
        width: 100%; /* Full width */
        padding: 15px 25px;
        background-color: var(--secondary-blue); /* Deeper blue for the button */
        color: var(--white);
        border: none;
        border-radius: 35px; /* More rounded */
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(45, 156, 219, 0.3); /* Shadow based on secondary blue */
        display: flex; /* For icon alignment */
        justify-content: center;
        align-items: center;
        gap: 10px; /* Space between text and icon */
        margin-top: 30px; /* Space above the button */
    }

    .btn-submit-app:hover {
        background-color: var(--primary-blue); /* Lighter on hover */
        box-shadow: 0 12px 25px rgba(45, 156, 219, 0.4);
        transform: translateY(-3px);
    }

    .btn-submit-app .fas { /* Style for Font Awesome icon in button */
        font-size: 1rem;
    }

    .form-contact-info {
        font-size: 0.95rem;
        color: var(--text-dark);
        margin-top: 30px;
        text-align: center;
    }
    .form-contact-info span {
        display: block; /* Make the phone number block level for spacing */
        font-weight: 600;
        color: var(--secondary-blue);
        margin-top: 5px;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .caregiver-apply-section {
            flex-direction: column; /* Stack columns on smaller screens */
            align-items: center;
            gap: 40px;
            padding: 40px 20px;
        }

        .caregiver-info-column,
        .caregiver-form-column {
            max-width: 100%; /* Take full width */
            width: 100%;
        }

        .caregiver-info-column {
            padding-top: 0;
            text-align: center; /* Center text in info column */
        }
        .caregiver-info-block { /* Adjust for older blocks that might be there if not fully removed */
            justify-content: center;
            align-items: center;
        }
        .caregiver-info-text {
            text-align: left; /* Keep text left-aligned within its own container */
        }
    }

    @media (max-width: 576px) {
        .caregiver-apply-section {
            padding: 30px 15px;
        }
        .caregiver-form-column {
            padding: 30px 20px;
        }
        .caregiver-info-column h2 {
            font-size: 2rem;
        }
        .caregiver-form-column h2 {
            font-size: 1.5rem;
        }
        /* No need for form-row-halves adjustments as they are removed */
    }

</style>

<div class="caregiver-apply-section">
    {{-- Left Column: Information --}}
    <div class="caregiver-info-column">
        <h2>Join Our Compassionate Care Team</h2>
        <p class="caregiver-commitment-text">
            At CareLink, we empower caregivers to provide exceptional support to those who need it most. Join a community that values dedication, empathy, and professionalism, and help us transform healthcare together, one life at a time.
        </p>

        <h3>Why Choose CareLink?</h3>
        <ul class="caregiver-benefits-list">
            <li>Flexible Scheduling & Work-Life Balance</li>
            <li>Competitive Compensation & Benefits</li>
            <li>Dedicated Support & Continuous Learning</li>
            <li>Meaningful Work with Direct Impact</li>
            <li>Be Part of a Supportive Community</li>
        </ul>

        <h3 style="margin-top: 40px;">Our Simple Application Process:</h3>
        <ol class="application-process-steps">
            <li><strong>Submit Your Details:</strong> Complete the form on the right with your information.</li>
            <li><strong>Application Review:</strong> Our team will carefully review your qualifications and experience.</li>
            <li><strong>Onboarding:</strong> Successful applicants will be sent an interview mail to the platform via their email.</li>
        </ol>
    </div>

    {{-- Right Column: Application Form --}}
    <div class="caregiver-form-column">
        <h2>Complete the form</h2>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('caregiver.apply.store') }}">
            @csrf

            {{-- Full Name (Single, full-width input) --}}
            <div class="form-group">
                <label for="name" class="form-label">{{ ('Full Name') }}</label>
                <input type="text" class="form-control-custom @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- Phone Number (Full-width input) --}}
            <div class="form-group">
                <label for="phone_number" class="form-label">{{ ('Phone Number') }}</label>
                <input type="text" class="form-control-custom @error('contact_info') is-invalid @enderror" id="phone_number" name="contact_info" value="{{ old('contact_info') }}" required>
                @error('contact_info')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email Address (Full-width input) --}}
            <div class="form-group">
                <label for="email" class="form-label">{{ ('Email Address') }}</label>
                <input type="email" class="form-control-custom @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Experience (Full-width textarea) --}}
            <div class="form-group">
                <label for="experience" class="form-label">{{ ('Experience (Optional)') }}</label>
                <textarea class="form-control-custom @error('experience') is-invalid @enderror" id="experience" name="experience" rows="3">{{ old('experience') }}</textarea>
                <div class="form-text">e.g., "5 years experience in elder care, First Aid certified"</div>
                @error('experience')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Reason for Applying (Full-width textarea) --}}
            <div class="form-group">
                <label for="reason" class="form-label">{{ ('Reason for Applying (Optional)') }}</label>
                <textarea class="form-control-custom @error('reason') is-invalid @enderror" id="reason" name="reason" rows="5">{{ old('reason') }}</textarea>
                <div class="form-text">Tell us why you want to be a caregiver.</div>
                @error('reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-submit-app">{{ __('Submit Application') }} <i class="fas fa-arrow-right"></i></button>

            <p class="form-contact-info">
                Or contact us directly via
                <span><i class="fas fa-phone-alt"></i> +234 912-443-9394 </span> {{-- Example Nigerian number --}}
            </p>
        </form>
    </div>
</div>
@endsection