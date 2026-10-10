<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Klinik Sehat - @yield('title', 'Masuk')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900 min-h-screen flex flex-col">

{{-- NAVBAR --}}
<nav class="bg-white border-b border-gray-200 h-16 flex items-center px-4 lg:px-6 sticky top-0 z-50 shadow-sm">
    <a href="{{ url('/') }}" class="flex items-center gap-3">
        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </div>
        <span class="font-bold text-lg text-gray-800">Klinik Sehat</span>
    </a>

    <div class="ml-auto flex items-center gap-2">
        @auth
            <a href="{{ url('/dashboard') }}"
               class="text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-xl transition">
                Dashboard
            </a>
        @else
            @if (! request()->routeIs('login'))
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2 rounded-xl transition">
                    Masuk
                </a>
            @endif
            @if (Route::has('register') && ! request()->routeIs('register'))
                <a href="{{ route('register') }}"
                   class="text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-xl transition">
                    Daftar
                </a>
            @endif
        @endauth
    </div>
</nav>

{{-- CONTENT --}}
<main class="flex-1 flex items-center justify-center p-4 lg:p-8">
    {{ $slot }}
</main>

{{-- FOOTER --}}
<footer class="pb-6 text-center">
    <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Klinik Sehat. Pelayanan cepat, ramah, dan terpercaya.</p>
</footer>

</body>
</html>
