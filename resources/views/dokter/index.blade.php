@extends('layouts.app')

@section('title', 'Data Dokter')

@section('header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Dokter</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Manajemen Data Dokter</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola identitas, spesialisasi, dan jadwal praktik seluruh dokter klinik</p>
    </div>
    <div class="flex items-center gap-3">
        @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->hasRole('admin')))
        <button type="button"
                @click="showCreateModal = true"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all duration-150">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Dokter Baru</span>
        </button>
        @elseif(auth()->check() && auth()->user()->role === 'pasien')
        <a href="{{ route('pendaftaran.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all duration-150">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Buat Janji Kunjungan</span>
        </a>
        @endif
    </div>
</div>
@endsection

@section('content')
<div x-data="dokterManagement()"
     x-init="boot({{ json_encode(['openCreate' => $openCreate ?? false, 'openEdit' => $openEdit ?? null]) }})"
     class="space-y-6">

    {{-- ALERT FLASH MESSAGES --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
         class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        </div>
        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition
         class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-sm">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold">Terdapat kesalahan dalam pengisian formulir:</h3>
                    <ul class="mt-1 list-disc list-inside text-sm space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{-- STATISTIC SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Dokter -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Dokter</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($total ?? 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Terdaftar di sistem klinik</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Dokter Spesialis -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Dokter Spesialis</p>
                    <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($totalSpesialis ?? 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ ($total ?? 0) > 0 ? round((($totalSpesialis ?? 0) / $total) * 100) : 0 }}% dari total dokter
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.077 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Dokter Umum -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Dokter Umum</p>
                    <h3 class="text-2xl font-bold text-sky-600 mt-1">{{ number_format($totalUmum ?? 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ ($total ?? 0) > 0 ? round((($totalUmum ?? 0) / $total) * 100) : 0 }}% dari total dokter
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Dokter Aktif / Hadir -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Dokter Aktif / Hadir</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalAktif ?? 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ ($total ?? 0) > 0 ? round((($totalAktif ?? 0) / $total) * 100) : 0 }}% siap melayani pasien
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER & SEARCH TOOLBAR --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
        <form method="GET" action="{{ route('dokter.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            {{-- Search Input --}}
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari berdasarkan Nama Dokter, NIP/STR, atau Spesialisasi..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>

            {{-- Spesialisasi Filter --}}
            <div class="w-full md:w-52">
                <select name="spesialisasi"
                        class="w-full py-2.5 px-3.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                    <option value="">Semua Spesialisasi</option>
                    @foreach($spesialisasiList as $spesialisasi)
                    <option value="{{ $spesialisasi }}" {{ request('spesialisasi') == $spesialisasi ? 'selected' : '' }}>
                        {{ $spesialisasi }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'spesialisasi']))
                <a href="{{ route('dokter.index') }}"
                   class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors flex items-center justify-center"
                   title="Reset Filter">
                    Reset
                </a>
                @endif
            </div>
        </form>

        @if(request('search') || request('spesialisasi'))
        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
            <span>Filter aktif:</span>
            @if(request('search'))
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-blue-50 text-blue-700">
                    Pencarian: "{{ request('search') }}"
                </span>
            @endif
            @if(request('spesialisasi'))
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-indigo-50 text-indigo-700">
                    Spesialisasi: {{ request('spesialisasi') }}
                </span>
            @endif
        </div>
        @endif
    </div>

    {{-- DOCTOR DATA TABLE --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Daftar Dokter Terdaftar</h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Menampilkan {{ $dokters->firstItem() ?? 0 }} - {{ $dokters->lastItem() ?? 0 }} dari {{ $dokters->total() }} total dokter
                </p>
            </div>
            <div class="text-xs text-gray-400">
                Pembaruan data otomatis
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/75 text-xs uppercase text-gray-500 tracking-wider border-b border-gray-200/60 font-semibold">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 w-12 text-center">No</th>
                        <th scope="col" class="py-3.5 px-4">Informasi Dokter</th>
                        <th scope="col" class="py-3.5 px-4">Spesialisasi</th>
                        <th scope="col" class="py-3.5 px-4">Kontak</th>
                        <th scope="col" class="py-3.5 px-4">Jadwal Praktik</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Status</th>
                        <th scope="col" class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dokters as $index => $dokter)
                    <tr class="hover:bg-blue-50/30 transition-colors duration-150">
                        {{-- No --}}
                        <td class="py-4 px-4 text-center text-xs font-medium text-gray-400">
                            {{ $dokters->firstItem() + $index }}
                        </td>

                        {{-- Dokter Info --}}
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($dokter->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <button type="button"
                                            @click="openDetailModal({{ json_encode($dokter) }})"
                                            class="font-semibold text-gray-900 hover:text-blue-600 text-left transition-colors">
                                        {{ $dokter->nama }}
                                    </button>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="inline-flex items-center font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                            NIP/STR: {{ $dokter->nip ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Spesialisasi --}}
                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($dokter->spesialisasi === 'Umum')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200/60">
                                {{ $dokter->spesialisasi }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-200/60">
                                {{ $dokter->spesialisasi }}
                            </span>
                            @endif
                        </td>

                        {{-- Kontak --}}
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium text-gray-800">{{ $dokter->no_telepon }}</span>
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $dokter->no_telepon);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}"
                                   target="_blank"
                                   class="text-emerald-600 hover:text-emerald-700 transition-colors p-0.5 rounded"
                                   title="Kirim Pesan WhatsApp">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.353.101.173.45 0.742.965 1.201.662.591 1.221.774 1.394.861.173.086.275.072.376-.044.101-.116.433-.506.549-.679.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.203c.043.072.043.419-.101.824z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>

                        {{-- Jadwal Praktik --}}
                        <td class="py-4 px-4 max-w-xs">
                            <p class="text-xs text-gray-600" title="{{ $dokter->jadwal_praktik }}">
                                {{ $dokter->jadwal_praktik ?: '-' }}
                            </p>
                        </td>

                        {{-- Status --}}
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            @if($dokter->status === 'aktif')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                Non-Aktif
                            </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Detail: Semua Role --}}
                                <button type="button"
                                        @click="openDetailModal({{ json_encode($dokter) }})"
                                        class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Lihat Detail Dokter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>

                                {{-- Aksi Khusus Admin: Edit & Hapus --}}
                                @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->hasRole('admin')))
                                    {{-- Edit --}}
                                    <button type="button"
                                            @click="openEditModal({{ json_encode($dokter) }})"
                                            class="p-2 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Ubah Data Dokter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>

                                    {{-- Hapus --}}
                                    <button type="button"
                                            @click="openDeleteModal({{ json_encode($dokter) }})"
                                            class="p-2 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition-colors"
                                            title="Hapus Dokter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif

                                {{-- Aksi Pasien: Tombol Cepat Daftar Janji Temu --}}
                                @if(auth()->check() && auth()->user()->role === 'pasien')
                                    <a href="{{ route('pendaftaran.create') }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors"
                                       title="Daftar ke dokter ini">
                                        <span>Daftar</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center">
                            <div class="max-w-sm mx-auto flex flex-col items-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-800">Tidak ada data dokter</h3>
                                <p class="text-xs text-gray-500 mt-1 mb-4 text-center">
                                    @if(request('search') || request('spesialisasi'))
                                        Tidak ditemukan data yang cocok dengan kriteria filter pencarian Anda.
                                    @else
                                        Belum ada dokter terdaftar dalam sistem klinik saat ini.
                                    @endif
                                </p>
                                @if(request('search') || request('spesialisasi'))
                                    <a href="{{ route('dokter.index') }}"
                                       class="px-4 py-2 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                                        Reset Filter Pencarian
                                    </a>
                                @else
                                    <a href="{{ route('dokter.create') }}"
                                       class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">
                                        + Tambah Dokter Sekarang
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($dokters->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $dokters->links() }}
        </div>
        @endif
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 1: TAMBAH DOKTER BARU                              --}}
    {{-- ======================================================== --}}
    <div x-show="showCreateModal"
         x-cloak
         @keydown.escape.window="showCreateModal = false"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

        <div x-show="showCreateModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showCreateModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden border border-gray-100">

            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Tambah Dokter Baru</h3>
                        <p class="text-xs text-gray-500">Lengkapi data identitas dan jadwal praktik dokter klinik</p>
                    </div>
                </div>
                <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('dokter.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="create_nama" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="create_nama"
                               name="nama"
                               required
                               value="{{ old('nama') }}"
                               placeholder="Contoh: dr. Andi Pratama, Sp.A"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- NIP / STR --}}
                    <div>
                        <label for="create_nip" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            NIP / STR
                        </label>
                        <input type="text"
                               id="create_nip"
                               name="nip"
                               maxlength="50"
                               value="{{ old('nip') }}"
                               placeholder="Nomor Induk Pegawai / Registrasi"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        <span class="text-[11px] text-gray-400 mt-1 block">Kosongkan jika belum memiliki NIP/STR</span>
                    </div>

                    {{-- Spesialisasi --}}
                    <div>
                        <label for="create_spesialisasi" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Spesialisasi <span class="text-rose-500">*</span>
                        </label>
                        <select id="create_spesialisasi"
                                name="spesialisasi"
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">Pilih Spesialisasi</option>
                            @foreach($spesialisasiList as $spesialisasi)
                            <option value="{{ $spesialisasi }}" {{ old('spesialisasi') == $spesialisasi ? 'selected' : '' }}>
                                {{ $spesialisasi }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- No. Telepon --}}
                    <div>
                        <label for="create_no_telepon" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            No. Telepon <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel"
                               id="create_no_telepon"
                               name="no_telepon"
                               required
                               maxlength="15"
                               value="{{ old('no_telepon') }}"
                               placeholder="Contoh: 081234567890"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Jadwal Praktik --}}
                    <div>
                        <label for="create_jadwal_praktik" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Jadwal Praktik
                        </label>
                        <input type="text"
                               id="create_jadwal_praktik"
                               name="jadwal_praktik"
                               maxlength="255"
                               value="{{ old('jadwal_praktik') }}"
                               placeholder="Contoh: Senin - Jumat, 08:00 - 14:00"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-[11px] text-gray-400 mt-1 block">Tulis hari dan jam praktik dokter</span>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="create_status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="create_status"
                                name="status"
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="aktif" {{ old('status') == 'aktif' || !old('status') ? 'selected' : '' }}>Aktif</option>
                            <option value="non-aktif" {{ old('status') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button"
                            @click="showCreateModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                        Simpan Dokter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 2: UBAH / EDIT DOKTER                              --}}
    {{-- ======================================================== --}}
    <div x-show="showEditModal"
         x-cloak
         @keydown.escape.window="showEditModal = false"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

        <div x-show="showEditModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showEditModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden border border-gray-100">

            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-amber-50 to-orange-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Perbarui Data Dokter</h3>
                        <p class="text-xs text-gray-500">Edit informasi identitas, spesialisasi, dan jadwal dokter</p>
                    </div>
                </div>
                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form :action="editActionUrl" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="edit_nama" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="edit_nama"
                               name="nama"
                               required
                               x-model="editForm.nama"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    {{-- NIP / STR --}}
                    <div>
                        <label for="edit_nip" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            NIP / STR
                        </label>
                        <input type="text"
                               id="edit_nip"
                               name="nip"
                               maxlength="50"
                               x-model="editForm.nip"
                               placeholder="Nomor Induk Pegawai / Registrasi"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 font-mono">
                        <span class="text-[11px] text-gray-400 mt-1 block">Kosongkan jika belum memiliki NIP/STR</span>
                    </div>

                    {{-- Spesialisasi --}}
                    <div>
                        <label for="edit_spesialisasi" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Spesialisasi <span class="text-rose-500">*</span>
                        </label>
                        <select id="edit_spesialisasi"
                                name="spesialisasi"
                                required
                                x-model="editForm.spesialisasi"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                            <option value="">Pilih Spesialisasi</option>
                            @foreach($spesialisasiList as $spesialisasi)
                            <option value="{{ $spesialisasi }}">{{ $spesialisasi }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- No. Telepon --}}
                    <div>
                        <label for="edit_no_telepon" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            No. Telepon <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel"
                               id="edit_no_telepon"
                               name="no_telepon"
                               required
                               maxlength="15"
                               x-model="editForm.no_telepon"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    {{-- Jadwal Praktik --}}
                    <div>
                        <label for="edit_jadwal_praktik" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Jadwal Praktik
                        </label>
                        <input type="text"
                               id="edit_jadwal_praktik"
                               name="jadwal_praktik"
                               maxlength="255"
                               x-model="editForm.jadwal_praktik"
                               placeholder="Contoh: Senin - Jumat, 08:00 - 14:00"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="edit_status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="edit_status"
                                name="status"
                                required
                                x-model="editForm.status"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                            <option value="aktif">Aktif</option>
                            <option value="non-aktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button"
                            @click="showEditModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 3: DETAIL PROFIL DOKTER                            --}}
    {{-- ======================================================== --}}
    <div x-show="showDetailModal"
         x-cloak
         @keydown.escape.window="showDetailModal = false"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

        <div x-show="showDetailModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showDetailModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-100">

            {{-- Header Profil --}}
            <div class="p-6 text-center border-b border-gray-100 relative bg-gradient-to-b from-blue-50/80 to-white">
                <button type="button"
                        @click="showDetailModal = false"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <div class="w-20 h-20 mx-auto rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold shadow-inner"
                     x-text="getInitials(detailData.nama)">
                </div>

                <h3 class="text-xl font-bold text-gray-900 mt-3" x-text="detailData.nama"></h3>
                <div class="flex items-center justify-center gap-2 mt-1 flex-wrap">
                    <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2.5 py-0.5 rounded-full" x-text="'NIP/STR: ' + (detailData.nip || '-')"></span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold bg-violet-50 text-violet-700" x-text="detailData.spesialisasi"></span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold"
                          :class="detailData.status === 'aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                          x-text="detailData.status === 'aktif' ? 'Aktif' : 'Non-Aktif'"></span>
                </div>
            </div>

            {{-- Body Details --}}
            <div class="p-6 space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Spesialisasi</p>
                        <p class="text-sm font-medium text-gray-800 mt-0.5" x-text="detailData.spesialisasi || '-'"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Status Dokter</p>
                        <p class="text-sm font-bold mt-0.5"
                           :class="detailData.status === 'aktif' ? 'text-emerald-600' : 'text-rose-600'"
                           x-text="detailData.status === 'aktif' ? 'Aktif' : 'Non-Aktif'"></p>
                    </div>
                </div>

                <div class="pb-4 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase">No. Telepon</p>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm font-medium text-gray-800" x-text="detailData.no_telepon"></span>
                        <a :href="'https://wa.me/' + formatPhoneForWa(detailData.no_telepon)"
                           target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition-colors">
                            <span>Hubungi WA</span>
                        </a>
                    </div>
                </div>

                <div class="pb-4 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase">Jadwal Praktik</p>
                    <p class="text-sm text-gray-700 mt-1 leading-relaxed" x-text="detailData.jadwal_praktik || '-'"></p>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">NIP / STR</p>
                    <p class="text-sm font-mono text-gray-800 mt-1" x-text="detailData.nip || '-'"></p>
                </div>

                <div class="pt-2 text-xs text-gray-400 flex justify-between">
                    <span>Terdaftar: <span x-text="formatDate(detailData.created_at)"></span></span>
                    <span x-show="detailData.id" x-text="'ID: #' + detailData.id"></span>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <button type="button"
                        @click="showDetailModal = false; openEditModal(detailData)"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 hover:text-amber-800 hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Ubah Data Ini</span>
                </button>
                <button type="button"
                        @click="showDetailModal = false"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 4: KONFIRMASI HAPUS DOKTER                         --}}
    {{-- ======================================================== --}}
    <div x-show="showDeleteModal"
         x-cloak
         @keydown.escape.window="showDeleteModal = false"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

        <div x-show="showDeleteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showDeleteModal = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100">

            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Hapus Data Dokter?</h3>
                <p class="text-sm text-gray-500 mt-2">
                    Apakah Anda yakin ingin menghapus data dokter:
                </p>
                <div class="mt-3 p-3 bg-gray-50 rounded-xl text-left">
                    <p class="font-bold text-gray-900 text-sm" x-text="deleteData.nama"></p>
                    <p class="text-xs text-gray-500 font-mono mt-0.5" x-text="'NIP/STR: ' + (deleteData.nip || '-')"></p>
                </div>
                <p class="text-xs text-rose-600 mt-3">
                    Data yang dihapus akan dipindahkan ke status soft-delete dan tidak akan tampil di daftar aktif.
                </p>
            </div>

            <form :action="deleteActionUrl" method="POST" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button"
                        @click="showDeleteModal = false"
                        class="px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-200/50 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                    Ya, Hapus Dokter
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function dokterManagement() {
    return {
        showCreateModal: false,
        showEditModal: false,
        showDetailModal: false,
        showDeleteModal: false,

        editActionUrl: '',
        deleteActionUrl: '',

        editForm: {
            id: null,
            nama: '',
            nip: '',
            spesialisasi: '',
            no_telepon: '',
            jadwal_praktik: '',
            status: 'aktif'
        },

        detailData: {},
        deleteData: {},

        boot(options) {
            if (options.openEdit) {
                this.openEditModal(options.openEdit);
            } else if (options.openCreate) {
                this.showCreateModal = true;
            }
        },

        openEditModal(dokter) {
            this.editActionUrl = '/dokter/' + dokter.id;
            this.editForm = {
                id: dokter.id,
                nama: dokter.nama || '',
                nip: dokter.nip || '',
                spesialisasi: dokter.spesialisasi || '',
                no_telepon: dokter.no_telepon || '',
                jadwal_praktik: dokter.jadwal_praktik || '',
                status: dokter.status || 'aktif'
            };
            this.showEditModal = true;
        },

        openDetailModal(dokter) {
            this.detailData = dokter;
            this.showDetailModal = true;
        },

        openDeleteModal(dokter) {
            this.deleteData = dokter;
            this.deleteActionUrl = '/dokter/' + dokter.id;
            this.showDeleteModal = true;
        },

        getInitials(name) {
            if (!name) return 'DK';
            const parts = name.trim().split(' ');
            if (parts.length >= 2) {
                return (parts[0][0] + parts[1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            try {
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
            } catch (e) {
                return dateStr;
            }
        },

        formatPhoneForWa(phone) {
            if (!phone) return '';
            let cleaned = phone.replace(/[^0-9]/g, '');
            if (cleaned.startsWith('0')) {
                cleaned = '62' + cleaned.substring(1);
            }
            return cleaned;
        }
    };
}
</script>
@endsection
