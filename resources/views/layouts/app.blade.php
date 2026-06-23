<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        @stack('meta')
    <title>@yield('title', 'Issue Tracker')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="bg-gray-800 shadow">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-6">
            <a href="{{ route('projects.index') }}" class="text-white font-semibold text-lg">Issue Tracker</a>
            <a href="{{ route('projects.index') }}" class="text-gray-300 hover:text-white text-sm">Projects</a>
            <a href="{{ route('tags.index') }}" class="text-gray-300 hover:text-white text-sm">Tags</a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>

