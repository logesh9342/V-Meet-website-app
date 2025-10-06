<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="auth-id" content="{{ Auth::id() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @endauth

    <title>V-Meet</title>

        <!-- Fonts -->

        <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/chat.js', 'resources/js/meeting.js', 'resources/js/schedule.js', 'resources/js/fileshare.js'])
    @stack('styles')
    <style>
        body { background: #f3f6fb; font-family: 'Segoe UI', Arial, sans-serif; margin: 0; }
        .jey-meet-index { display: flex; min-height: 80vh; }
        .sidebar { width: 220px; background: #2b2d42; color: #fff; padding: 2rem 1rem; display: flex; flex-direction: column; align-items: flex-start; }
        .logo { font-size: 2rem; font-weight: 700; margin-bottom: 2rem; letter-spacing: 2px; }
        nav { display: flex; flex-direction: column; gap: 1.2rem; width: 100%; }
        nav a { color: #fff; text-decoration: none; font-size: 1.1rem; padding: 0.5rem 1rem; border-radius: 6px; transition: background 0.2s; }
        nav a:hover { background: #3a3f5a; }
        .main-content { flex: 1; padding: 3rem; }
    </style>
    </head>
    <body>
        <div id="app">
            @yield('content')
        </div>
    </body>
</html>
