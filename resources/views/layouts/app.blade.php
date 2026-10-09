<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Klinik Sehat - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900">

{{-- NAVBAR --}}
<nav class="bg-white border-b border-gray-200 h-16 flex items-center px-6 sticky top-0 z-50">
    <div class="flex items-center gap-3">
        <span class="text-2xl">🏥</span>
        <span class="font-bold text-lg text-gray-800 tracking-tight">Klinik Sehat</span>
    </div>

    <div class="ml-auto flex items-center gap-4">
        @auth
        <div class="text-right flex items-center gap-3">
            <div>
                <p class="text-sm font-semibold text-gray-800 leading-tight">{{ auth()->user()->name }}</p>
                <div class="flex justify-end mt-0.5">
                    @php
                        $role = auth()->user()->role ?? 'pasien';
                    @endphp
                    @if($role === 'admin')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-100 text-indigo-700 uppercase tracking-wide">
                            Admin
                        </span>
                    @elseif($role === 'dokter')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wide">
                            Dokter
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-sky-100 text-sky-700 uppercase tracking-wide">
                            Pasien
                        </span>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700 font-medium transition-colors">
                    Logout
                </button>
            </form>
        </div>
        @endauth
    </div>
</nav>

<div class="flex min-h-[calc(100vh-4rem)]">
    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r border-gray-200 p-4 shrink-0 flex flex-col justify-between">
        <div>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider px-3 mb-2">Menu Utama</p>

            <nav class="space-y-1">
                {{-- Dashboard: Semua Role --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>

                {{-- Pasien: Hanya Admin (Master Data Pasien) --}}
                @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->hasRole('admin')))
                <a href="{{ route('pasien.index') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('pasien.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span>👥</span>
                    <span>Data Pasien</span>
                </a>
                @endif

                {{-- Dokter: Semua Role (Admin = Kelola, Dokter & Pasien = Jadwal & Informasi) --}}
                <a href="{{ route('dokter.index') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('dokter.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span>🩺</span>
                    <span>{{ auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->hasRole('admin')) ? 'Data Dokter' : 'Jadwal Dokter' }}</span>
                </a>

                {{-- Pendaftaran: Semua Role --}}
                <a href="{{ route('pendaftaran.index') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('pendaftaran.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span>📅</span>
                    <span>{{ auth()->check() && auth()->user()->role === 'pasien' ? 'Daftar Kunjungan' : 'Pendaftaran' }}</span>
                </a>

                {{-- Rekam Medis: Semua Role --}}
                <a href="{{ route('rekam-medis.index') }}"
                   class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('rekam-medis.*') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span>📋</span>
                    <span>Rekam Medis</span>
                </a>
            </nav>
        </div>

        {{-- Footer Sidebar Profil --}}
        <div class="pt-4 border-t border-gray-100">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl text-xs font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Pengaturan Profil</span>
            </a>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 p-6 lg:p-8 max-w-full overflow-x-hidden">
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
