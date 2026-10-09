<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Klinik Sehat - @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">

{{-- NAVBAR --}}
<nav class="bg-white border-b border-gray-200 h-16 flex items-center px-4 lg:px-6 sticky top-0 z-50 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </div>
        <span class="font-bold text-lg text-gray-800">Klinik Sehat</span>
    </div>

    <div class="ml-auto flex items-center gap-4">
        <div class="text-right hidden sm:block">
            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name ?? 'Guest' }}</p>
            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role ?? '' }}</p>
        </div>
        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
            {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm text-rose-600 hover:text-rose-800 font-medium">Logout</button>
        </form>
    </div>
</nav>

<div class="flex">
    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r border-gray-200 min-h-[calc(100vh-4rem)] p-4 hidden md:block">
        <p class="text-xs font-semibold text-gray-400 uppercase px-4 mb-2">Menu Utama</p>

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm mb-1 transition
           {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' }}">
            <span>📊</span> Dashboard
        </a>

        <a href="{{ route('pasien.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm mb-1 transition
           {{ request()->routeIs('pasien.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' }}">
            <span>👥</span> Pasien
        </a>

        <a href="{{ route('dokter.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm mb-1 transition
           {{ request()->routeIs('dokter.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' }}">
            <span>🩺</span> Dokter
        </a>

        <a href="{{ route('pendaftaran.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm mb-1 transition
           {{ request()->routeIs('pendaftaran.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' }}">
            <span>📅</span> Pendaftaran
        </a>

        <a href="{{ route('rekam-medis.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm mb-1 transition
           {{ request()->routeIs('rekam-medis.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100' }}">
            <span>📋</span> Rekam Medis
        </a>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 p-4 lg:p-6 max-w-full overflow-x-hidden">
        @hasSection('header')
            @yield('header')
        @else
            <h1 class="text-2xl font-bold text-gray-800 mb-6">@yield('title', 'Dashboard')</h1>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>

