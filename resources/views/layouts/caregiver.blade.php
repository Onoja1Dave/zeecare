<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CareLink Caregiver Dashboard') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom Styles for the FAB Menu */
        .fab-menu-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1050; /* Above most content, below modals */
            width: 60px; /* Make it a circle */
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
            transition: all 0.3s ease;
        }

        .fab-menu-button:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        .fab-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9); /* Dark, semi-transparent background */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1060; /* Above FAB button */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease, visibility 0.4s ease;
            backdrop-filter: blur(8px); /* Modern blur effect */
            -webkit-backdrop-filter: blur(8px); /* Safari support */
        }

        .fab-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .fab-overlay-content {
            text-align: center;
            color: #fff;
        }

        .fab-overlay-content .nav-link {
            font-size: 2.5rem; /* Larger, bolder links */
            font-weight: 700;
            padding: 1rem 0;
            display: block;
            color: rgba(255, 255, 255, 0.7); /* Slightly muted white */
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .fab-overlay-content .nav-link:hover,
        .fab-overlay-content .nav-link.active-fab {
            color: #fff; /* Bright white on hover/active */
            transform: scale(1.05);
            text-shadow: 0 0 10px rgba(255,255,255,0.5); /* Subtle glow */
        }

        .fab-overlay-content .nav-link i {
            margin-right: 15px; /* Space between icon and text */
            font-size: 2rem; /* Larger icons */
        }

        .fab-overlay .close-btn {
            position: absolute;
            top: 30px;
            right: 30px;
            font-size: 2rem;
            color: #fff;
            background: none;
            border: none;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .fab-overlay .close-btn:hover {
            transform: rotate(90deg);
        }
    </style>
</head>
<body>
    <div id="app">
        {{-- Main Navbar (Minimal) --}}
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="CareLink Logo" class="logo-svg" style="height: 40px;">
                </a>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    {{-- All main navigation links are now in the FAB menu --}}
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
                                    <a class="nav-link" href="{{ route('login') }}">{{ ('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ ('Register') }}</a>
                                </li>
                            @endif
                        @else
                            {{-- Messages Icon/Count --}}
                            <li class="nav-item me-3">
                                <a class="nav-link position-relative" href="{{ route('caregiver.messages.index') }}">
                                    <i class="fas fa-comments fs-5"></i>
                                    @if (isset($pendingMessagesNav) && $pendingMessagesNav > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $pendingMessagesNav }}
                                            <span class="visually-hidden">unread messages</span>
                                        </span>
                                    @endif
                                </a>
                            </li>

                            {{-- Notifications Icon/Count --}}
                            <li class="nav-item me-3">
                                <a class="nav-link position-relative" href="{{ route('caregiver.notifications.index') }}">
                                    <i class="fas fa-bell fs-5"></i>
                                    @if (isset($unreadNotificationsNav) && $unreadNotificationsNav > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                            {{ $unreadNotificationsNav }}
                                            <span class="visually-hidden">unread notifications</span>
                                        </span>
                                    @endif
                                </a>
                            </li>

                          <li class="nav-item dropdown">
    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="{{ route('logout') }}" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ Auth::user()->name }}
    </a>

    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{ route('caregiver.profile.edit') }}">
            <i class="fas fa-user-circle me-2"></i> Profile
        </a>
        <a class="dropdown-item" href="{{ route('logout') }}"
           onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt me-2"></i> {{ ('Logout') }}
        </a>
        {{-- The logout form needs to be directly within the dropdown-menu,
             and must contain the @csrf directive --}}
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf {{-- THIS IS CRUCIAL FOR SECURITY --}}
        </form>
    </div>
</li> 
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Main content area --}}
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    {{-- Floating Action Button (FAB) --}}
    <button class="btn btn-primary fab-menu-button" id="fabMenuToggle">
        <i class="fas fa-bars"></i> {{-- Hamburger icon --}}
    </button>

    {{-- Full-screen FAB Overlay Menu --}}
    <div class="fab-overlay" id="fabOverlayMenu">
        <button class="close-btn" id="fabCloseButton"><i class="fas fa-times"></i></button>
        <div class="fab-overlay-content">
            <ul class="list-unstyled">
                <li>
                    <a class="nav-link {{ request()->routeIs('caregiver.dashboard') ? 'active-fab' : '' }}" href="{{ route('caregiver.dashboard') }}">
                        <i class="fas fa-house"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('caregiver.patients.index') ? 'active-fab' : '' }}" href="{{ route('caregiver.patients.index') }}">
                        <i class="fas fa-users"></i> My Patients
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('caregiver.tasks.index') ? 'active-fab' : '' }}" href="{{ route('caregiver.tasks.index') }}">
                        <i class="fas fa-list-check"></i> Tasks
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('caregiver.messages.index') || request()->routeIs('caregiver.messages.show') ? 'active-fab' : '' }}" href="{{ route('caregiver.messages.index') }}">
                        <i class="fas fa-comments"></i> Messages
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('caregiver.notifications.index') ? 'active-fab' : '' }}" href="{{ route('caregiver.notifications.index') }}">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('caregiver.profile.edit') }}">
                        <i class="fas fa-user-circle"></i> Profile Settings
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Bootstrap Bundle with Popper --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    {{-- Dark Mode Toggle Logic (remains unchanged) --}}
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

            // FAB Menu Toggle Logic
            const fabMenuToggle = document.getElementById('fabMenuToggle');
            const fabOverlayMenu = document.getElementById('fabOverlayMenu');
            const fabCloseButton = document.getElementById('fabCloseButton');

            fabMenuToggle.addEventListener('click', function() {
                fabOverlayMenu.classList.add('show');
            });

            fabCloseButton.addEventListener('click', function() {
                fabOverlayMenu.classList.remove('show');
            });

            // Close menu if a link is clicked (optional, but good UX for single-page apps or internal navigation)
            fabOverlayMenu.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    fabOverlayMenu.classList.remove('show');
                });
            });
        });
    </script>

    @stack('scripts')

</body>
</html>