@extends('layouts.app')

@section('title', 'Data Pendaftaran')

@section('header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Pendaftaran</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Manajemen Pendaftaran Pasien</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola antrean kunjungan, jadwal konsultasi dokter, dan status pelayanan klinik</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('pendaftaran.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all duration-150">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Daftar Pasien Baru</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div x-data="pendaftaranManagement()" class="space-y-6">

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
        <!-- Card 1: Total Pendaftaran -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pendaftaran</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($total ?? $pendaftarans->total()) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Keseluruhan nomor antrean</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Menunggu Antrean -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menunggu</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($totalMenunggu ?? 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ ($total ?? 0) > 0 ? round((($totalMenunggu ?? 0) / $total) * 100) : 0 }}% dalam antrean periksa
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Sedang Diproses -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sedang Diproses</p>
                    <h3 class="text-2xl font-bold text-sky-600 mt-1">{{ number_format($totalDiproses ?? 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ ($total ?? 0) > 0 ? round((($totalDiproses ?? 0) / $total) * 100) : 0 }}% sedang konsultasi
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Selesai Dilayani -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm hover:shadow transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Selesai</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalSelesai ?? 0) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ ($total ?? 0) > 0 ? round((($totalSelesai ?? 0) / $total) * 100) : 0 }}% pemeriksaan tuntas
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
        <form method="GET" action="{{ route('pendaftaran.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
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
                       placeholder="Cari Kode Daftar, Nama Pasien, Dokter, atau Keluhan..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>

            {{-- Status Filter --}}
            <div class="w-full md:w-44">
                <select name="status"
                        class="w-full py-2.5 px-3.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>⚡ Diproses</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>❌ Batal</option>
                </select>
            </div>

            {{-- Dokter Filter --}}
            <div class="w-full md:w-52">
                <select name="dokter_id"
                        class="w-full py-2.5 px-3.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                    <option value="">Semua Dokter</option>
                    @foreach($dokters as $d)
                        <option value="{{ $d->id }}" {{ request('dokter_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->nama }} ({{ $d->spesialisasi }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Filter --}}
            <div class="w-full md:w-44">
                <input type="date"
                       name="tgl_kunjungan"
                       value="{{ request('tgl_kunjungan') }}"
                       class="w-full py-2.5 px-3.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
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

                @if(request()->hasAny(['search', 'status', 'dokter_id', 'tgl_kunjungan']))
                <a href="{{ route('pendaftaran.index') }}"
                   class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors flex items-center justify-center"
                   title="Reset Filter">
                    Reset
                </a>
                @endif
            </div>
        </form>

        @if(request('search') || request('status') || request('dokter_id') || request('tgl_kunjungan'))
        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center gap-2 text-xs text-gray-500">
            <span>Filter aktif:</span>
            @if(request('search'))
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-blue-50 text-blue-700">
                    Pencarian: "{{ request('search') }}"
                </span>
            @endif
            @if(request('status'))
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-amber-50 text-amber-700 capitalize">
                    Status: {{ request('status') }}
                </span>
            @endif
            @if(request('dokter_id'))
                @php $activeDoc = $dokters->firstWhere('id', request('dokter_id')); @endphp
                @if($activeDoc)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-indigo-50 text-indigo-700">
                    Dokter: {{ $activeDoc->nama }}
                </span>
                @endif
            @endif
            @if(request('tgl_kunjungan'))
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-emerald-50 text-emerald-700">
                    Tgl: {{ date('d/m/Y', strtotime(request('tgl_kunjungan'))) }}
                </span>
            @endif
        </div>
        @endif
    </div>

    {{-- REGISTRATION DATA TABLE --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Daftar Pendaftaran Antrean</h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Menampilkan {{ $pendaftarans->firstItem() ?? 0 }} - {{ $pendaftarans->lastItem() ?? 0 }} dari {{ $pendaftarans->total() }} total pendaftaran
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button"
                        @click="openCreateModal()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Pendaftaran Cepat</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/75 text-xs uppercase text-gray-500 tracking-wider border-b border-gray-200/60 font-semibold">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 w-12 text-center">No</th>
                        <th scope="col" class="py-3.5 px-4">Kode & Jadwal Kunjungan</th>
                        <th scope="col" class="py-3.5 px-4">Data Pasien</th>
                        <th scope="col" class="py-3.5 px-4">Dokter & Poli</th>
                        <th scope="col" class="py-3.5 px-4">Keluhan Utama</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Status</th>
                        <th scope="col" class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendaftarans as $index => $item)
                    <tr class="hover:bg-blue-50/30 transition-colors duration-150">
                        {{-- No --}}
                        <td class="py-4 px-4 text-center text-xs font-medium text-gray-400">
                            {{ $pendaftarans->firstItem() + $index }}
                        </td>

                        {{-- Kode & Jadwal --}}
                        <td class="py-4 px-4">
                            <div class="space-y-1">
                                <button type="button"
                                        @click="openDetailModal({{ json_encode($item) }})"
                                        class="font-mono font-bold text-sm text-blue-600 hover:text-blue-800 hover:underline text-left block">
                                    {{ $item->kode_daftar }}
                                </button>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ $item->tgl_kunjungan ? $item->tgl_kunjungan->format('d/m/Y') : '-' }}</span>
                                    <span class="text-gray-300">•</span>
                                    <span class="font-medium text-gray-700">{{ substr($item->jam_kunjungan, 0, 5) }} WIB</span>
                                </div>
                            </div>
                        </td>

                        {{-- Pasien Info --}}
                        <td class="py-4 px-4">
                            @if($item->pasien)
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $item->pasien->jenis_kelamin == 'L' ? 'bg-sky-100 text-sky-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ strtoupper(substr($item->pasien->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $item->pasien->nama }}</p>
                                    <p class="text-xs text-gray-400 font-mono mt-0.5">NIK: {{ $item->pasien->nik }}</p>
                                </div>
                            </div>
                            @else
                            <span class="text-xs text-gray-400 italic">Pasien Terhapus</span>
                            @endif
                        </td>

                        {{-- Dokter & Spesialisasi --}}
                        <td class="py-4 px-4">
                            @if($item->dokter)
                            <div class="space-y-0.5">
                                <p class="font-medium text-gray-900 text-sm leading-tight">{{ $item->dokter->nama }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">
                                    🩺 {{ $item->dokter->spesialisasi }}
                                </span>
                            </div>
                            @else
                            <span class="text-xs text-gray-400 italic">Dokter belum dipilih</span>
                            @endif
                        </td>

                        {{-- Keluhan --}}
                        <td class="py-4 px-4 max-w-xs">
                            <p class="text-xs text-gray-700 line-clamp-2" title="{{ $item->keluhan }}">
                                {{ $item->keluhan }}
                            </p>
                        </td>

                        {{-- Status Badge --}}
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            @if($item->status == 'menunggu')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu
                            </span>
                            @elseif($item->status == 'diproses')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                Diproses
                            </span>
                            @elseif($item->status == 'selesai')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Selesai
                            </span>
                            @elseif($item->status == 'batal')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                <span>✕</span> Batal
                            </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Detail --}}
                                <button type="button"
                                        @click="openDetailModal({{ json_encode($item) }})"
                                        class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors"
                                        title="Lihat Detail Pendaftaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>

                                {{-- Ubah / Update Status --}}
                                <button type="button"
                                        @click="openEditModal({{ json_encode($item) }})"
                                        class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-colors"
                                        title="Ubah Status & Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>

                                {{-- Hapus --}}
                                <button type="button"
                                        @click="openDeleteModal({{ json_encode($item) }})"
                                        class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors"
                                        title="Hapus Pendaftaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center">
                            <div class="max-w-sm mx-auto space-y-3">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 mx-auto">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900">Belum ada data pendaftaran</h3>
                                <p class="text-xs text-gray-500">
                                    @if(request()->hasAny(['search', 'status', 'dokter_id', 'tgl_kunjungan']))
                                        Tidak ada pendaftaran yang cocok dengan kriteria filter pencarian Anda.
                                    @else
                                        Mulai daftarkan kunjungan pasien baru untuk pemeriksaan dokter di klinik.
                                    @endif
                                </p>
                                <div class="pt-2">
                                    <a href="{{ route('pendaftaran.create') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-colors">
                                        <span>Buat Pendaftaran Pertama</span>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($pendaftarans->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $pendaftarans->links() }}
        </div>
        @endif
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 1: TAMBAH PENDAFTARAN CEPAT                        --}}
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
             class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden border border-gray-100">
            
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Pendaftaran Antrean Baru</h3>
                        <p class="text-xs text-gray-500">Pilih pasien dan dokter tujuan kunjungan</p>
                    </div>
                </div>
                <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('pendaftaran.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                {{-- Pilih Pasien --}}
                <div>
                    <label for="create_pasien_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Pilih Pasien <span class="text-rose-500">*</span>
                    </label>
                    <select id="create_pasien_id"
                            name="pasien_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">-- Pilih Pasien Terdaftar --</option>
                        @foreach($pasiens as $p)
                            <option value="{{ $p->id }}" {{ old('pasien_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }} (NIK: {{ $p->nik }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">
                        Pasien belum ada? <a href="{{ route('pasien.create') }}" class="text-blue-600 hover:underline">Tambah data pasien baru terlebih dahulu</a>.
                    </p>
                </div>

                {{-- Pilih Dokter --}}
                <div>
                    <label for="create_dokter_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Dokter Pemeriksa <span class="text-rose-500">*</span>
                    </label>
                    <select id="create_dokter_id"
                            name="dokter_id"
                            required
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">-- Pilih Dokter & Spesialisasi --</option>
                        @foreach($dokters as $d)
                            <option value="{{ $d->id }}" {{ old('dokter_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->nama }} — Spesialis {{ $d->spesialisasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jadwal: Tanggal & Jam --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="create_tgl_kunjungan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Tanggal Kunjungan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date"
                               id="create_tgl_kunjungan"
                               name="tgl_kunjungan"
                               required
                               value="{{ old('tgl_kunjungan', date('Y-m-d')) }}"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="create_jam_kunjungan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Jam Kunjungan <span class="text-rose-500">*</span>
                        </label>
                        <input type="time"
                               id="create_jam_kunjungan"
                               name="jam_kunjungan"
                               required
                               value="{{ old('jam_kunjungan', date('H:i')) }}"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Keluhan Utama --}}
                <div>
                    <label for="create_keluhan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Keluhan Utama Pasien <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="create_keluhan"
                              name="keluhan"
                              rows="3"
                              required
                              placeholder="Deskripsikan gejala, keluhan kesehatan, atau tujuan kunjungan..."
                              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('keluhan') }}</textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button"
                            @click="showCreateModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                        Simpan Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 2: UBAH DATA & STATUS PENDAFTARAN                  --}}
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
             class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden border border-gray-100">
            
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-amber-50 to-orange-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Ubah Status & Pendaftaran</h3>
                        <p class="text-xs text-gray-500" x-text="'Kode: ' + editForm.kode_daftar"></p>
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

                {{-- Pasien Display (Readonly) --}}
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Pasien Terdaftar</p>
                    <p class="text-sm font-bold text-gray-900 mt-0.5" x-text="editForm.pasien_nama"></p>
                </div>

                {{-- Status Pendaftaran --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                        Status Kunjungan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <label class="flex items-center justify-center gap-1.5 p-2.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-800 text-xs font-semibold text-gray-600">
                            <input type="radio" name="status" value="menunggu" x-model="editForm.status" class="sr-only">
                            <span>⏳ Menunggu</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-800 text-xs font-semibold text-gray-600">
                            <input type="radio" name="status" value="diproses" x-model="editForm.status" class="sr-only">
                            <span>⚡ Diproses</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-800 text-xs font-semibold text-gray-600">
                            <input type="radio" name="status" value="selesai" x-model="editForm.status" class="sr-only">
                            <span>✅ Selesai</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2.5 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 has-[:checked]:text-rose-800 text-xs font-semibold text-gray-600">
                            <input type="radio" name="status" value="batal" x-model="editForm.status" class="sr-only">
                            <span>❌ Batal</span>
                        </label>
                    </div>
                </div>

                {{-- Dokter --}}
                <div>
                    <label for="edit_dokter_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Dokter Pemeriksa <span class="text-rose-500">*</span>
                    </label>
                    <select id="edit_dokter_id"
                            name="dokter_id"
                            x-model="editForm.dokter_id"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        @foreach($dokters as $d)
                            <option value="{{ $d->id }}">
                                {{ $d->nama }} ({{ $d->spesialisasi }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jadwal --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_tgl_kunjungan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Tanggal Kunjungan
                        </label>
                        <input type="date"
                               id="edit_tgl_kunjungan"
                               name="tgl_kunjungan"
                               x-model="editForm.tgl_kunjungan"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="edit_jam_kunjungan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Jam Kunjungan
                        </label>
                        <input type="time"
                               id="edit_jam_kunjungan"
                               name="jam_kunjungan"
                               x-model="editForm.jam_kunjungan"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Keluhan --}}
                <div>
                    <label for="edit_keluhan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                        Keluhan Pasien
                    </label>
                    <textarea id="edit_keluhan"
                              name="keluhan"
                              rows="3"
                              x-model="editForm.keluhan"
                              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button"
                            @click="showEditModal = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 3: DETAIL PENDAFTARAN LENGKAP                      --}}
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
            
            {{-- Header Detail --}}
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-blue-50 via-sky-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Detail Tiket Pendaftaran</h3>
                        <p class="font-mono text-xs text-blue-600 font-semibold" x-text="detailData.kode_daftar"></p>
                    </div>
                </div>
                <button type="button" @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body Detail --}}
            <div class="p-6 space-y-4 text-sm">
                {{-- Status Banner --}}
                <div class="flex items-center justify-between p-3.5 rounded-xl border"
                     :class="{
                        'bg-amber-50 border-amber-200 text-amber-800': detailData.status === 'menunggu',
                        'bg-blue-50 border-blue-200 text-blue-800': detailData.status === 'diproses',
                        'bg-emerald-50 border-emerald-200 text-emerald-800': detailData.status === 'selesai',
                        'bg-rose-50 border-rose-200 text-rose-800': detailData.status === 'batal'
                     }">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider">Status Antrean Saat Ini</p>
                        <p class="font-bold text-base capitalize mt-0.5" x-text="detailData.status"></p>
                    </div>
                    <span class="text-2xl"
                          x-text="detailData.status === 'menunggu' ? '⏳' : (detailData.status === 'diproses' ? '⚡' : (detailData.status === 'selesai' ? '✅' : '❌'))">
                    </span>
                </div>

                {{-- Pasien Section --}}
                <div class="pb-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase">Informasi Pasien</p>
                    <div class="flex items-center justify-between mt-1">
                        <div>
                            <p class="font-bold text-gray-900 text-base" x-text="detailData.pasien ? detailData.pasien.nama : '-'"></p>
                            <p class="text-xs text-gray-500 font-mono" x-text="'NIK: ' + (detailData.pasien ? detailData.pasien.nik : '-')"></p>
                        </div>
                        <div x-show="detailData.pasien && detailData.pasien.no_telp">
                            <a :href="'https://wa.me/' + formatPhoneForWa(detailData.pasien ? detailData.pasien.no_telp : '')"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition-colors">
                                <span>Hubungi WA</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Dokter Section --}}
                <div class="pb-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase">Dokter Pemeriksa</p>
                    <div class="mt-1">
                        <p class="font-bold text-gray-900" x-text="detailData.dokter ? detailData.dokter.nama : '-'"></p>
                        <p class="text-xs text-gray-500" x-text="'Spesialisasi: ' + (detailData.dokter ? detailData.dokter.spesialisasi : '-')"></p>
                    </div>
                </div>

                {{-- Jadwal Kunjungan --}}
                <div class="grid grid-cols-2 gap-4 pb-3 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Tanggal Kunjungan</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5" x-text="formatDate(detailData.tgl_kunjungan)"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase">Jam Kedatangan</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5" x-text="(detailData.jam_kunjungan ? detailData.jam_kunjungan.substring(0, 5) : '-') + ' WIB'"></p>
                    </div>
                </div>

                {{-- Keluhan Pasien --}}
                <div class="pb-3 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-400 uppercase">Keluhan Utama</p>
                    <div class="mt-1.5 p-3 bg-gray-50 rounded-xl text-gray-700 text-sm leading-relaxed" x-text="detailData.keluhan || '-'"></div>
                </div>

                {{-- Rekam Medis Status --}}
                <div x-show="detailData.rekam_medis" class="p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                    <p class="text-xs font-semibold text-emerald-800 uppercase">Rekam Medis Tersedia</p>
                    <p class="text-xs text-emerald-700 mt-0.5">Pasien sudah menerima diagnosa dari dokter.</p>
                </div>
            </div>

            {{-- Footer Detail --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <button type="button"
                        @click="showDetailModal = false; openEditModal(detailData)"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 hover:text-amber-800 hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Ubah Status</span>
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
    {{-- MODAL 4: KONFIRMASI HAPUS PENDAFTARAN                    --}}
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
                <h3 class="text-lg font-bold text-gray-900">Hapus Data Pendaftaran?</h3>
                <p class="text-sm text-gray-500 mt-2">
                    Apakah Anda yakin ingin membatalkan & menghapus nomor pendaftaran:
                </p>
                <div class="mt-3 p-3 bg-gray-50 rounded-xl text-left">
                    <p class="font-bold font-mono text-blue-600 text-sm" x-text="deleteData.kode_daftar"></p>
                    <p class="text-xs text-gray-700 mt-0.5" x-text="'Pasien: ' + (deleteData.pasien ? deleteData.pasien.nama : '-')"></p>
                </div>
                <p class="text-xs text-rose-600 mt-3">
                    Tindakan ini tidak dapat dibatalkan. Antrean pasien akan dihapus dari sistem.
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
                    Ya, Hapus Pendaftaran
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function pendaftaranManagement() {
    return {
        showCreateModal: false,
        showEditModal: false,
        showDetailModal: false,
        showDeleteModal: false,

        editActionUrl: '',
        deleteActionUrl: '',

        editForm: {
            id: null,
            kode_daftar: '',
            pasien_nama: '',
            dokter_id: '',
            tgl_kunjungan: '',
            jam_kunjungan: '',
            status: 'menunggu',
            keluhan: ''
        },

        detailData: {},
        deleteData: {},

        openCreateModal() {
            this.showCreateModal = true;
        },

        openEditModal(item) {
            this.editActionUrl = '/pendaftaran/' + item.id;
            this.editForm = {
                id: item.id,
                kode_daftar: item.kode_daftar || '',
                pasien_nama: item.pasien ? item.pasien.nama : '-',
                dokter_id: item.dokter_id || '',
                tgl_kunjungan: item.tgl_kunjungan ? item.tgl_kunjungan.substring(0, 10) : '',
                jam_kunjungan: item.jam_kunjungan ? item.jam_kunjungan.substring(0, 5) : '',
                status: item.status || 'menunggu',
                keluhan: item.keluhan || ''
            };
            this.showEditModal = true;
        },

        openDetailModal(item) {
            this.detailData = item;
            this.showDetailModal = true;
        },

        openDeleteModal(item) {
            this.deleteData = item;
            this.deleteActionUrl = '/pendaftaran/' + item.id;
            this.showDeleteModal = true;
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
