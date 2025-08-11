<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CareLink - Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Custom Color Variables & Global Styles (consistent with welcome page) */
        :root {
            --primary-blue: #44DCDC;       /* Vibrant Light Blue */
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

        /* Login Card Container */
        .login-card {
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow-soft);
            padding: 40px;
            max-width: 450px; /* Smaller max-width for a more contained feel */
            width: 100%;
            text-align: center; /* Center content within card */
        }
        .login-card .logo {
            margin-bottom: 25px;
            display: inline-flex; /* To center the inline elements of logo */
            align-items: center;
        }
        .login-card .logo-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-right: 10px;
            line-height: 1;
        }
        .login-card .logo-text-care {
            color: var(--primary-blue);
            font-weight: 800;
            font-size: 1.8rem;
            line-height: 1;
        }
        .login-card .logo-text-link {
            color: var(--secondary-blue);
            font-weight: 800;
            font-size: 1.8rem;
            line-height: 1;
        }
        .login-card .logo-tagline {
            display: block;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: -5px;
            font-weight: 400;
            opacity: 0.9;
            white-space: nowrap; /* Prevent tagline from wrapping */
        }

        .login-card h2 {
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
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            font-size: 1rem;
            color: var(--text-dark);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input[type="email"]:focus,
        .form-group input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px var(--input-focus);
        }

        /* Radio Button Group */
        .role-radio-group {
            margin-top: 25px;
            margin-bottom: 30px;
            text-align: left; /* Align radios left */
        }
        .role-radio-group p {
            font-size: 1.05rem;
            color: var(--text-dark);
            margin-bottom: 15px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .role-radio-option {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .role-radio-option input[type="radio"] {
            /* Customize radio button appearance */
            appearance: none; /* Hide default radio button */
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid var(--input-border);
            border-radius: 50%;
            margin-right: 12px;
            cursor: pointer;
            position: relative;
            flex-shrink: 0; /* Prevent shrinking on small screens */
            transition: border-color 0.2s ease;
        }
        .role-radio-option input[type="radio"]:checked {
            border-color: var(--primary-blue);
        }
        .role-radio-option input[type="radio"]:checked::before {
            content: '';
            display: block;
            width: 10px;
            height: 10px;
            background-color: var(--primary-blue);
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .role-radio-option label {
            margin-bottom: 0; /* Override default label margin */
            cursor: pointer;
            font-weight: 400;
            color: var(--text-dark);
            font-size: 1rem;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px; /* Adjust margin as role radios are before it */
            margin-bottom: 30px;
            font-size: 0.9rem;
        }
        .form-options .remember-me {
            display: flex;
            align-items: center;
            color: var(--text-muted);
        }
        .form-options .remember-me input[type="checkbox"] {
            margin-right: 8px;
            transform: scale(1.1); /* Slightly larger checkbox */
            accent-color: var(--primary-blue); /* Color for checkbox */
        }
        .form-options .forgot-password a {
            color: var(--text-muted);
            font-weight: 500;
        }
        .form-options .forgot-password a:hover {
            color: var(--primary-blue);
        }

        /* Login Button */
        .btn-login {
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
            margin-bottom: 20px;
        }
        .btn-login:hover {
            background-color: var(--secondary-blue);
            box-shadow: 0 12px 25px rgba(71, 182, 237, 0.4);
            transform: translateY(-3px);
        }

        /* Register Link */
        .register-link {
            font-size: 0.95rem;
            color: var(--text-muted);
        }
        .register-link a {
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
            .login-card {
                padding: 30px 20px;
                max-width: 380px; /* Slightly tighter on small screens */
            }
            .login-card h2 {
                font-size: 1.8rem;
            }
            .btn-login {
                font-size: 1rem;
                padding: 12px 20px;
            }
            .form-options {
                flex-direction: column;
                align-items: flex-start;
            }
            .form-options .forgot-password {
                margin-top: 10px;
            }
        }
        @media (max-width: 480px) {
            .login-card .logo {
                flex-direction: column; /* Stack logo elements on very small screens */
                align-items: center;
            }
            .login-card .logo-icon {
                margin-right: 0;
                margin-bottom: 5px;
            }
            .login-card .logo-tagline {
                white-space: normal; /* Allow tagline to wrap if needed */
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        {{-- Your CareLink Logo --}}
        <a class="logo" href="/">
           
            <img src="{{ asset('images/logo.svg') }}" alt="CareLink Logo" style="height: 40px;">
            
        </a>

        <h2>Welcome Back!</h2>

        {{-- Laravel Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <div class="role-radio-group">
                <p>I am signing in as:</p>
                <div class="role-radio-option">
                    <input type="radio" id="role_patient" name="role" value="patient" {{ old('role') == 'patient' ? 'checked' : '' }} required>
                    <label for="role_patient">Patient</label>
                </div>
                <div class="role-radio-option">
                    <input type="radio" id="role_doctor" name="role" value="doctor" {{ old('role') == 'doctor' ? 'checked' : '' }}>
                    <label for="role_doctor">Doctor</label>
                </div>
                <div class="role-radio-option">
                    <input type="radio" id="role_caregiver" name="role" value="caregiver" {{ old('role') == 'caregiver' ? 'checked' : '' }}>
                    <label for="role_caregiver">Caregiver</label>
                </div>
            </div>

            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>
                <div class="forgot-password">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                    @endif
                </div>
            </div>

            <button type="submit" class="btn-login">Login</button>

            <p class="register-link">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
        </form>
    </div>
</body>
</html>