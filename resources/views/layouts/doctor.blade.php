<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CareLink Doctor Dashboard') }}</title>

    {{-- 1. Custom Fonts (Inter and Plus Jakarta Sans) --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    {{-- 2. Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- 3. Icon Libraries (Font Awesome for consistency) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- 4. Your Custom app.css (LOAD THIS LAST for overrides and dark mode) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="CareLink Logo" class="logo-svg" style="height: 40px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    {{-- Left-side nav items (Doctor-specific dashboard links) --}}
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctor.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctor.patients.index') }}">My Patients</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctor.appointments.index') }}">Appointments</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctor.prescriptions') }}">Prescriptions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('doctor.notes.index') }}">Patient Notes</a>
                        </li>
                    </ul>

                    {{-- Right-side nav items --}}
                    <ul class="navbar-nav ms-auto align-items-center">
                        {{-- Dark Mode Toggle --}}
                        <li class="nav-item me-3">
                            <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary dark-mode-toggle" title="Toggle Dark Mode">
                                <i class="fas fa-moon light-icon"></i>
                                <i class="fas fa-sun dark-icon d-none"></i>
                            </button>
                        </li>
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            {{-- Messages Icon/Count (Using Font Awesome for consistency) --}}
                            <li class="nav-item me-3">
                                <a class="nav-link position-relative" href="{{ route('doctor.messages.index') }}">
                                    <i class="fas fa-comments fs-5"></i> {{-- Font Awesome for messages --}}
                                    @if (isset($pendingMessagesNav) && $pendingMessagesNav > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $pendingMessagesNav }}
                                            <span class="visually-hidden">unread messages</span>
                                        </span>
                                    @endif
                                </a>
                            </li>

                            {{-- Notifications Icon/Count (Using Font Awesome for consistency) --}}
                            <li class="nav-item me-3">
                                <a class="nav-link position-relative" href="{{ route('doctor.notifications.index') }}">
                                    <i class="fas fa-bell fs-5"></i> {{-- Font Awesome for notifications --}}
                                    @if (isset($unreadNotificationsNav) && $unreadNotificationsNav > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                            {{ $unreadNotificationsNav }}
                                            <span class="visually-hidden">unread notifications</span>
                                        </span>
                                    @endif
                                </a>
                            </li>

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('doctor.profile.edit') }}"> {{-- Assuming a profile route --}}
                                        <i class="fas fa-user-circle me-2"></i> Profile
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    {{-- Bootstrap Bundle with Popper --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    {{-- Dark Mode Toggle Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const body = document.body;
            const lightIcon = darkModeToggle.querySelector('.light-icon');
            const darkIcon = darkModeToggle.querySelector('.dark-icon');

            function setDarkMode(isDark) {
                if (isDark) {
                    body.classList.add('dark-mode');
                    lightIcon.classList.add('d-none');
                    darkIcon.classList.remove('d-none');
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    body.classList.remove('dark-mode');
                    lightIcon.classList.remove('d-none');
                    darkIcon.classList.add('d-none');
                    localStorage.setItem('darkMode', 'disabled');
                }
            }

            if (localStorage.getItem('darkMode') === 'enabled') {
                setDarkMode(true);
            } else {
                setDarkMode(false);
            }

            darkModeToggle.addEventListener('click', function (e) {
                e.preventDefault();
                if (body.classList.contains('dark-mode')) {
                    setDarkMode(false);
                } else {
                    setDarkMode(true);
                }
            });
        });
    </script>

    @stack('scripts')

</body>
</html>