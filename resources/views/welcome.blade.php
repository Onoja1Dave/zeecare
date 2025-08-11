<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CareLink - Transforming Healthcare Together</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Custom Color Variables & Global Styles */
        :root {
            --primary-blue: #44DCDC;       /* Vibrant Light Blue */
            --secondary-blue:rgb(50, 129, 129);     /* Deeper Blue */
            --accent-green: #5CB85C;       /* Health Green */
            --background-light: #F9FAFC;   /* Very light background */
            --white: #FFFFFF;
            --text-dark: #333333;
            --text-muted: #666666;
            --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.08); /* Soft, diffused shadow */
            --shadow-hover: 0 15px 45px rgba(0, 0, 0, 0.15); /* Deeper shadow on hover */
            --light-blue-bg-section: #EBF8FF; /* Background for sections like FAQ */
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: var(--background-light);
            line-height: 1.7; /* Slightly more spacious line-height */
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark); /* Headings in dark text, some accents */
            font-weight: 700;
        }

        a {
            color: var(--primary-blue);
            text-decoration: none;
        }
        a:hover {
            color: var(--secondary-blue);
            text-decoration: underline;
        }

        /* Navbar Styling */
        .navbar {
            background-color: var(--white) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); /* More pronounced shadow */
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand .logo-icon {
            font-size: 2.5rem; /* Larger icon */
            color: var(--primary-blue);
            margin-right: 10px;
            line-height: 1; /* Align with text */
        }
        .navbar-brand .logo-text-care {
            color: var(--primary-blue);
            font-weight: 800; /* Extra bold */
            font-size: 1.8rem;
            line-height: 1;
        }
        .navbar-brand .logo-text-link {
            color: var(--secondary-blue);
            font-weight: 800;
            font-size: 1.8rem;
            line-height: 1;
        }
        .navbar-brand .logo-tagline {
            display: block;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: -5px;
            font-weight: 400;
            opacity: 0.9;
        }
        .navbar .nav-link {
            font-weight: 500;
            color: var(--text-muted) !important;
            margin: 0 15px;
            transition: color 0.3s ease;
        }
        .navbar .nav-link:hover, .navbar .nav-link.active {
            color: var(--primary-blue) !important;
        }
        .navbar .btn {
            border-radius: 30px; /* More pronounced rounded corners */
            padding: 0.65rem 1.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Subtle button shadow */
        }
        .navbar .btn-outline-primary { /* Login button (outline for subtle secondary) */
            color: var(--secondary-blue);
            border-color: var(--secondary-blue);
            background-color: transparent;
        }
        .navbar .btn-outline-primary:hover {
            background-color: var(--secondary-blue);
            color: var(--white);
            box-shadow: 0 6px 15px rgba(45, 156, 219, 0.3);
        }
        .navbar .btn-primary-filled { /* Sign Up button (filled, primary brand color) */
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            color: var(--white);
            margin-left: 15px;
        }
        .navbar .btn-primary-filled:hover {
            background-color: var(--secondary-blue);
            border-color: var(--secondary-blue);
            box-shadow: 0 6px 15px rgba(71, 182, 237, 0.3);
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 700px; /* Adjusted height to match design feel */
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), /* Dark overlay for text readability */
                        url('{{ asset('images/herosection.png') }}') no-repeat center center / cover; /* Placeholder image */
            /* !!! IMPORTANT: Replace 'images/hero-placeholder.jpg' with your actual hero image path */
            color: var(--white);
            text-align: left; /* Text alignment from design */
        }
        .hero-content-wrapper {
            position: relative;
            z-index: 2; /* Above background and overlay */
            padding: 0 15px; /* Ensure content isn't flush */
            max-width: 1200px; /* Constrain content width */
            margin: 0 auto;
            width: 100%;
            display: flex;
            align-items: center; /* Vertically align content */
            justify-content: flex-start; /* Left align content */
        }
        .hero-text-content {
            max-width: 600px; /* Constrain text width as per design */
            margin-right: auto; /* Push content to left */
        }
        .hero-section h1 {
            font-size: 3.8rem; /* Adjusted for impact */
            line-height: 1.2;
            margin-bottom: 20px;
            color: var(--white);
            font-weight: 700;
        }
        .hero-section p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 30px;
        }
        .hero-section .btn-group .btn {
            font-size: 1.1rem;
            padding: 0.9rem 2.2rem;
            border-radius: 35px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hero-section .btn-get-started {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            color: var(--white);
            box-shadow: 0 8px 20px rgba(71, 182, 237, 0.3);
            margin-right: 15px;
        }
        .hero-section .btn-get-started:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(71, 182, 237, 0.4);
            background-color: var(--secondary-blue);
            border-color: var(--secondary-blue);
        }
        .hero-section .alert-caregiver {
            background-color: rgba(var(--white), 0.2); /* Semi-transparent white background */
            border: 1px solid rgba(var(--white), 0.4); /* Subtle border */
            border-radius: 10px; /* Rounded corners */
            padding: 15px 25px;
            margin-top: 30px; /* Spacing from buttons */
            display: inline-flex; /* To wrap content */
            align-items: center;
            backdrop-filter: blur(5px); /* Frosted glass effect */
            -webkit-backdrop-filter: blur(5px); /* For Safari */
        }
        .hero-section .alert-caregiver p {
            font-size: 1rem;
            margin-bottom: 0;
            color: var(--white);
            margin-right: 15px;
        }
        .hero-section .alert-caregiver .btn {
            background-color: var(--accent-green);
            border-color: var(--accent-green);
            color: var(--white);
            font-size: 0.9rem;
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(92, 184, 92, 0.3);
        }
        .hero-section .alert-caregiver .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(92, 184, 92, 0.4);
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        /* "Who CareLink Is For?" Section */
        .who-is-for-section {
            background-color: var(--white);
            padding: 100px 0;
            text-align: center;
        }
        .who-is-for-section h2 {
            font-size: 2.8rem;
            margin-bottom: 70px;
            color: var(--text-dark);
            font-weight: 700;
        }
        .role-card {
            background-color: var(--background-light);
            border-radius: 15px;
            box-shadow: var(--shadow-soft);
            padding: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }
        .role-card .image-placeholder {
            width: 150px; /* Adjust size of placeholder image */
            height: 150px;
            border-radius: 50%; /* Circular images */
            background-color: #e0f2f7; /* Light blue background for placeholder */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem; /* Placeholder icon size */
            color: var(--primary-blue);
            margin-bottom: 25px;
            overflow: hidden; /* Ensure images fit */
        }
        .role-card .image-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .role-card h3 {
            font-size: 1.6rem;
            margin-bottom: 15px;
            color: var(--secondary-blue);
            font-weight: 600;
        }
        .role-card p {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* About CareLink Section */
        .about-section {
            background-color: var(--background-light);
            padding: 80px 0;
            text-align: center;
        }
        .about-section h2 {
            font-size: 2.8rem;
            color: var(--text-dark);
            margin-bottom: 30px;
        }
        .about-section p {
            font-size: 1.15rem;
            color: var(--text-muted);
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
        }

        /* FAQ Section */
        .faq-section {
            background-color: var(--light-blue-bg-section); /* Light blue background as per design */
            padding: 100px 0;
        }
        .faq-section h2 {
            font-size: 2.8rem;
            color: var(--text-dark);
            margin-bottom: 60px;
            text-align: left; /* Left align as per design */
        }
        .accordion-button {
            background-color: var(--white) !important;
            color: var(--text-dark) !important;
            font-size: 1.15rem;
            font-weight: 600;
            border-radius: 10px !important;
            box-shadow: var(--shadow-soft);
            padding: 18px 25px;
            transition: all 0.3s ease;
        }
        .accordion-button:not(.collapsed) {
            color: var(--primary-blue) !important;
            background-color: var(--white) !important;
            box-shadow: var(--shadow-hover);
        }
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(71, 182, 237, 0.25); /* Primary blue focus ring */
        }
        .accordion-item {
            border: none !important;
            margin-bottom: 15px; /* Spacing between items */
            border-radius: 10px !important;
            overflow: hidden; /* For shadow */
        }
        .accordion-body {
            background-color: var(--white);
            color: var(--text-muted);
            font-size: 1rem;
            padding: 20px 25px;
            border-top: 1px solid #eee;
        }
        .accordion-item:last-of-type {
            margin-bottom: 0;
        }


        /* Coming Soon Section */
        .coming-soon-section {
            background-color: var(--white);
            padding: 100px 0;
            text-align: center;
        }
        .coming-soon-section h2 {
            font-size: 2.8rem;
            color: var(--text-dark);
            margin-bottom: 60px;
        }
        .phone-mockup-wrapper {
            max-width: 400px; /* Control size of phone mockup */
            margin: 0 auto;
            position: relative;
            padding: 20px; /* Space for shadow */
        }
        .phone-mockup-wrapper .phone-placeholder {
            width: 100%;
            height: 450px; /* Fixed height for consistency */
            background: url('{{ asset('images/phone-mockup-placeholder.png') }}') no-repeat center center / contain; /* Placeholder */
            /* !!! IMPORTANT: Replace 'images/phone-mockup-placeholder.png' with your actual phone mockup image path */
            box-shadow: var(--shadow-soft);
            border-radius: 20px;
        }
        .app-features-list {
            list-style: none;
            padding: 0;
            text-align: left;
            margin-top: 30px;
        }
        .app-features-list li {
            font-size: 1.1rem;
            color: var(--text-dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .app-features-list li i {
            color: var(--primary-blue);
            font-size: 1.2rem;
            margin-right: 15px;
        }
        .app-buttons .btn {
            font-size: 1.1rem;
            padding: 0.8rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            margin: 0 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .app-buttons .btn-appstore {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            color: var(--white);
        }
        .app-buttons .btn-appstore:hover {
            background-color: var(--secondary-blue);
            border-color: var(--secondary-blue);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(71, 182, 237, 0.3);
        }
        .app-buttons .btn-playstore {
            background-color: var(--accent-green);
            border-color: var(--accent-green);
            color: var(--white);
        }
        .app-buttons .btn-playstore:hover {
            background-color: #4CAF50;
            border-color: #4CAF50;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(92, 184, 92, 0.3);
        }
        /* Footer */
        footer {
            background-color: var(--light-blue-bg-section); /* Matching FAQ section background */
            color: var(--text-dark);
            padding: 60px 0;
        }
        footer .footer-logo .logo-icon, footer .footer-logo .logo-text-care, footer .footer-logo .logo-text-link {
            font-size: 1.5rem; /* Slightly smaller logo in footer */
        }
        footer .footer-logo .logo-tagline {
            font-size: 0.75rem;
        }
        footer h5 {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 1.3rem;
        }
        footer ul {
            list-style: none;
            padding: 0;
        }
        footer ul li {
            margin-bottom: 10px;
        }
        footer ul li a {
            color: var(--text-muted);
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }
        footer ul li a:hover {
            color: var(--primary-blue);
            text-decoration: underline;
        }
        .social-icons a {
            color: var(--text-muted);
            font-size: 1.4rem;
            margin-right: 15px;
            transition: transform 0.2s ease, color 0.3s ease;
        }
        .social-icons a:hover {
            transform: translateY(-3px);
            color: var(--primary-blue);
        }
        footer .copyright {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 30px;
        }

        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .hero-section h1 { font-size: 3.2rem; }
            .hero-section p { font-size: 1.15rem; }
            .who-is-for-section h2, .faq-section h2, .coming-soon-section h2, .about-section h2 { font-size: 2.5rem; }
            .role-card h3 { font-size: 1.4rem; }
            .role-card p { font-size: 1rem; }
            .accordion-button { font-size: 1.05rem; }
            .accordion-body { font-size: 0.95rem; }
            .app-features-list li { font-size: 1rem; }
            .app-buttons .btn { font-size: 1rem; padding: 0.7rem 1.8rem; }
            footer h5 { font-size: 1.2rem; }
        }

        @media (max-width: 992px) {
            .navbar-collapse {
                text-align: center;
                margin-top: 15px;
            }
            .navbar .nav-link { margin: 0 10px 10px 10px; }
            .navbar .btn { margin: 5px; }

            .hero-section { height: auto; padding: 100px 0; text-align: center; }
            .hero-content-wrapper { justify-content: center; }
            .hero-text-content { max-width: 700px; margin: 0 auto; }
            .hero-section h1 { font-size: 2.8rem; }
            .hero-section p { font-size: 1.05rem; }
            .hero-section .alert-caregiver { margin-top: 20px; justify-content: center; }

            .who-is-for-section h2, .faq-section h2, .coming-soon-section h2, .about-section h2 { font-size: 2.2rem; }
            .role-card { margin-bottom: 30px; }
            .faq-section h2 { text-align: center; }
            .coming-soon-section .row { flex-direction: column; }
            .phone-mockup-wrapper { margin-bottom: 40px; }
            .app-features-list { text-align: center; }
            .app-features-list li { justify-content: center; }
            .app-features-list li i { margin-right: 10px; }
            .app-buttons { margin-top: 30px; }
            .app-buttons .btn { margin: 5px; }

            footer .col-md-4 { margin-bottom: 30px; }
            footer .col-md-4:last-child { margin-bottom: 0; }
        }

        @media (max-width: 768px) {
            .hero-section h1 { font-size: 2.2rem; }
            .hero-section p { font-size: 0.95rem; }
            .hero-section .btn-group { flex-direction: column; }
            .hero-section .btn-group .btn { width: 80%; max-width: 300px; margin: 10px auto; }
            .hero-section .btn-get-started { margin-right: auto; }
            .hero-section .alert-caregiver { flex-direction: column; padding: 10px 15px; }
            .hero-section .alert-caregiver p { margin-right: 0; margin-bottom: 10px; text-align: center; }

            .who-is-for-section h2, .faq-section h2, .coming-soon-section h2, .about-section h2 { font-size: 1.8rem; }
            .about-section p { font-size: 1rem; }
            .accordion-button { font-size: 1rem; padding: 15px 20px; }
            .accordion-body { font-size: 0.9rem; }
            .phone-mockup-wrapper .phone-placeholder { height: 350px; }
            .app-features-list li { font-size: 0.95rem; }
            .app-features-list li i { font-size: 1rem; }
            .app-buttons .btn { font-size: 0.9rem; padding: 0.6rem 1.5rem; }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="/">
                 <img src="{{ asset('images/logo.svg') }}" alt="CareLink Logo" class="logo-svg" style="height: 40px;">   
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">HOME</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about-carelink-section">ABOUT US</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('caregiver.apply.form') }}">BECOME A CAREGIVER</a> </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">LOGIN</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary-filled">SIGNUP</a>
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <section class="hero-section">
        <div class="container hero-content-wrapper">
            <div class="hero-text-content">
                <h1>TRANSFORMING HEALTHCARE, TOGETHER.</h1>
                <p>Seamless patient management, enhanced communication, and smarter care delivery.</p>
                <div class="btn-group">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-get-started"><i class="fas fa-arrow-right me-2"></i> Get Started</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="who-is-for-section">
        <div class="container">
            <h2 class="mb-5">Why CareLink ?</h2>
            <div class="row g-4">
             <div class="col-md-6 col-lg-4">
                    <div class="role-card">
                        <div class="image-placeholder">
                            <img src="{{ asset('images/patient.png') }}" alt="For Patient"> 
                        </div>
                        <h3>For Patient</h3>
<p>
    CareLink simplifies your post-discharge recovery. Easily follow treatment plans and connect directly with your doctor and caregivers. It ensures you continue treatment correctly, offering crucial support. Designed for ease, it’s especially helpful for the elderly or those new to digital tools. Stay on track for a healthier recovery.
</p>                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="role-card">
                        <div class="image-placeholder">
                            <img src="{{ asset('images/doctor.png') }}" alt="For Doctors"> 
                        </div>
                        <h3>For Doctors</h3>
<p>
    Extend your vital care beyond the clinic with CareLink.
Monitor patient treatment adherence and progress remotely.
Streamline communication with patients and their caregivers for efficiency.
Gain critical insights to improve patient outcomes.
Reduce readmissions and ensure continuous, quality care.
</p>                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="role-card">
                        <div class="image-placeholder">
                            <img src="{{ asset('images/caregiver.png') }}" alt="For Caregivers"> 
                        </div>
                        <h3>For Caregiver</h3>
<p>
    CareLink empowers you to provide excellent support to your loved ones.
Access clear treatment plans and communicate directly with doctors.
Easily manage care, especially for elderly or digitally unfamiliar patients.
Ensure they follow their treatment plans correctly and safely.
Be the confident bridge in their recovery journey.
</p>                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about-carelink-section" class="about-section">
        <div class="container">
            <h2>ABOUT CARELINK</h2>
            <p>
CareLink is a web platform designed to improve post-discharge communication between doctors, patients, and caregivers. It ensures patients continue their treatment plans correctly, while also making room for patients who need extra support — especially the elderly or those unfamiliar with digital technology
        </p>
        </div>
    </section>

    <section id="faq" class="faq-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h2 class="mb-4">FREQUENTLY ASKED QUESTIONS</h2>
                </div>
                <div class="col-md-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    What is CareLink?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    CareLink is a comprehensive digital platform designed to streamline healthcare management and foster seamless communication among patients, caregivers, doctors, and administrators across Nigeria. It simplifies access to medical services and information.
                                       </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Who can benefit from using CareLink?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    CareLink is built for everyone involved in the healthcare journey: patients managing their health, caregivers coordinating support, doctors optimizing their practice, and administrators overseeing healthcare operations.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    How does CareLink ensure the security of my health data?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We prioritize your privacy and data security. CareLink employs advanced encryption protocols, secure servers, and strict access controls to ensure all personal and medical information is protected according to the highest industry standards.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    How do I get started with CareLink?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Getting started is easy! Simply click on the "Sign Up" button, select your user role (Patient, Caregiver, Doctor, or Administrator), and follow the prompts to create your account. You'll be connected in minutes.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    What kind of support does CareLink offer?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    CareLink provides dedicated support through our in-app messaging system, comprehensive FAQs, and a responsive customer service team ready to assist you with any questions or technical issues.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="coming-soon-section">
        <div class="container">
            <h2 class="mb-5">COMING SOON ON CARELINK</h2>
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="phone-mockup-wrapper">
                        
                             <img src="{{ asset('images/carelinkapp.png') }}" alt="CareLink Mobile App">
                        
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="app-features-list">
                        <h5>CareLink Mobile App</h5>
                        <li><i class="fas fa-comments"></i> Converse better with your doctor</li>
                        <li><i class="fas fa-phone-alt"></i> Call System</li>
                        <li><i class="fas fa-video"></i> Send live video or picture</li>
                        <li><i class="fas fa-users-gear"></i> Converse better with your caregiver</li>
                    </ul>
                    <div class="app-buttons mt-4">
                        <a href="#" class="btn btn-appstore"><i class="fab fa-apple me-2"></i> APPSTORE</a>
                        <a href="#" class="btn btn-playstore"><i class="fab fa-google-play me-2"></i> PLAYSTORE</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <a class="navbar-brand footer-logo" href="/">
                                        <img src="{{ asset('images/logo.svg') }}" alt="CareLink Logo" class="logo-svg" style="height: 40px;">   

                    </a>
                    <p class="small mt-3 text-muted">
                        CareLink is dedicated to transforming healthcare management and fostering seamless connections.
                    </p>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>CONTACT INFO</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">123 Akerele Street, wuse, Abuja</a></li>
                        <li><a href="mailto:info@carelink.com" class="text-muted">info@carelinkapp01.com</a></li>
                        <li><a href="tel:+2348001234567" class="text-muted">+234 9124439394</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h5>SOCIAL LINKS</h5>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>QUICK LINKS</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Home</a></li>
                        <li><a href="#about-carelink-section" class="text-muted">About Us</a></li>
                        <li><a href="#faq" class="text-muted">FAQ</a></li>
                        <li><a href="{{ route('caregiver.apply.form') }}" class="text-muted">Become A Caregiver</a></li>
                    </ul>
                </div>
            </div>
            <hr class="text-muted mt-5 mb-3">
            <div class="text-center copyright">
                <p>&copy; {{ date('Y') }} CareLink. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>