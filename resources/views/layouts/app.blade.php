<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Klinik Sehat - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

{{-- NAVBAR --}}
<nav class="bg-white border-b border-gray-200 h-16 flex items-center px-6 sticky top-0 z-50">
    <span class="font-bold text-lg text-gray-800">🏥 Klinik Sehat</span>
    <div class="ml-auto flex items-center gap-4">
        <div class="text-right">
            <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name ?? 'Guest' }}</p>
            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role ?? '' }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm text-red-600 hover:text-red-800 font-medium">Logout</button>
        </form>
    </div>
</nav>

<div class="flex">
    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r border-gray-200 min-h-[calc(100vh-4rem)] p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase px-4 mb-2">Menu Utama</p>

        <a href="{{ route('dashboard') }}"
           class="block px-4 py-2.5 rounded-lg text-sm mb-1 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            📊 Dashboard
        </a>

        <a href="{{ route('pasien.index') }}"
           class="block px-4 py-2.5 rounded-lg text-sm mb-1 {{ request()->routeIs('pasien.*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            👥 Pasien
        </a>

        <a href="{{ route('dokter.index') }}"
           class="block px-4 py-2.5 rounded-lg text-sm mb-1 {{ request()->routeIs('dokter.*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            🩺 Dokter
        </a>

        <a href="{{ route('pendaftaran.index') }}"
           class="block px-4 py-2.5 rounded-lg text-sm mb-1 {{ request()->routeIs('pendaftaran.*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            📅 Pendaftaran
        </a>

        <a href="{{ route('rekam-medis.index') }}"
           class="block px-4 py-2.5 rounded-lg text-sm mb-1 {{ request()->routeIs('rekam-medis.*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
            📋 Rekam Medis
        </a>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">@yield('title', 'Dashboard')</h1>
        @yield('content')
    </main>
</div>

</body>
</html>
