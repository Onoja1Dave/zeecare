<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CareLink - Register</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Custom Color Variables & Global Styles (consistent with login/welcome page) */
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
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: var(--background-light);
            line-height: 1.7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Full viewport height */
            margin: 0;
            padding: 20px; /* Padding for small screens */
            overflow-x: hidden; /* Prevent horizontal scroll */
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

        /* Register Card Container */
        .register-card { /* Using register-card for semantic clarity, but styling similar to login-card */
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow-soft);
            padding: 40px;
            max-width: 500px; /* Slightly wider than login card due to more fields */
            width: 100%;
            text-align: center; /* Center content within card */
        }
        .register-card .logo {
            margin-bottom: 25px;
            display: inline-flex; /* To center the inline elements of logo */
            align-items: center;
        }
        .register-card .logo-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-right: 10px;
            line-height: 1;
        }
        .register-card .logo-text-care {
            color: var(--primary-blue);
            font-weight: 800;
            font-size: 1.8rem;
            line-height: 1;
        }
        .register-card .logo-text-link {
            color: var(--secondary-blue);
            font-weight: 800;
            font-size: 1.8rem;
            line-height: 1;
        }
        .register-card .logo-tagline {
            display: block;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: -5px;
            font-weight: 400;
            opacity: 0.9;
            white-space: nowrap; /* Prevent tagline from wrapping */
        }

        .register-card h2 {
            font-size: 2.2rem;
            margin-bottom: 30px;
            color: var(--text-dark);
        }
       /* Form Styling */
        .form-group {
            margin-bottom: 20px;
            text-align: left; /* Align labels and inputs left */
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 0.95rem;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="date"],
        .form-group select { /* Apply to select as well */
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            font-size: 1rem;
            color: var(--text-dark);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            -webkit-appearance: none; /* Remove default select arrow */
            -moz-appearance: none;
            appearance: none; /* Remove default select arrow */
            background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2012%2012%22%3E%3Cpath%20fill%3D%22%23888%22%20d%3D%22M6%208.5L2%204.5h8L6%208.5z%22%2F%3E%3C%2Fsvg%3E'); /* Custom select arrow */
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 10px;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px var(--input-focus);
        }

        /* Register Button (same style as login) */
        .btn-register { /* Using btn-register class, but style matches btn-login */
            width: 100%;
            padding: 15px 25px;
            background-color: var(--primary-blue);
            color: var(--white);
            border: none;
            border-radius: 35px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(71, 182, 237, 0.3);
            margin-top: 20px; /* Added margin-top for spacing */
            margin-bottom: 20px;
        }
        .btn-register:hover {
            background-color: var(--secondary-blue);
            box-shadow: 0 12px 25px rgba(71, 182, 237, 0.4);
            transform: translateY(-3px);
        }

        /* Login Link */
        .login-link {
            font-size: 0.95rem;
            color: var(--text-muted);
        }
        .login-link a {
            font-weight: 600;
        }

        /* Laravel Validation Errors */
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
            font-size: 0.9rem;
        }
        .alert-danger ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .register-card {
                padding: 30px 20px;
                max-width: 450px;
            }
            .register-card h2 {
                font-size: 1.8rem;
            }
            .btn-register {
                font-size: 1rem;
                padding: 12px 20px;
            }
        }
        @media (max-width: 480px) {
            .register-card .logo {
                flex-direction: column; /* Stack logo elements on very small screens */
                align-items: center;
            }
            .register-card .logo-icon {
                margin-right: 0;
                 margin-bottom: 5px;
            }
            .register-card .logo-tagline {
                white-space: normal; /* Allow tagline to wrap if needed */
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="register-card">
       
        <a class="logo" href="/">
                       <img src="{{ asset('images/logo.svg') }}" alt="CareLink Logo" style="height: 40px;">

        </a>

        <h2>Create Your CareLink Account</h2>

        {{-- Laravel Validation Errors --}}
        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            </div>

            {{-- Hidden input for default role (Patient) --}}
            <input type="hidden" name="role" value="patient">

            {{-- Optional Fields --}}
            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender">
                    <option value="" {{ old('gender') == '' ? 'selected' : '' }}>Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" autocomplete="tel">
            </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_patient" id="is_patient" {{ old('is_patient') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_patient">
                                        {{ __('Register me as a Patient') }}
                                    </label>
                                </div>
                                @error('is_patient')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        

            <button type="submit" class="btn-register">Register Account</button>

            <p class="login-link">Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        </form>
    </div>
</body>
</html>