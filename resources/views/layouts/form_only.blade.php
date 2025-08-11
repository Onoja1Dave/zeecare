{{-- resources/views/layouts/only_form.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CareLink') }} - Caregiver Application</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- You can link to your main app.css here if it contains general styles
         that you want to carry over, but ensure it doesn't bring in unwanted dashboard elements.
         If your app.css is specifically for authenticated dashboards, you might omit it.
    --}}
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}

    {{-- Any additional global public styles can go here --}}

    {{-- The custom styles for the form itself will be included directly in the apply.blade.php
         as we did, or you can move them to a separate CSS file and link it here.
    --}}

</head>
<body>
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>

    {{-- Optional: If your layout.app includes app.js or other JS files,
                 and they are needed for your form (e.g., Bootstrap JS),
                 you might include them here or only if necessary.
    --}}
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}

</body>
</html>