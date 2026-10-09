@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Selamat Datang, {{ $user->name }}! 👋</h1>
                @if($user->isAdmin())
                    <p class="text-blue-100 mt-1 text-sm">Anda login sebagai <span class="font-semibold bg-white/20 px-2 py-0.5 rounded-lg">Administrator</span>. Kelola seluruh operasional klinik.</p>
                @elseif($user->isDokter())
                    <p class="text-blue-100 mt-1 text-sm">Anda login sebagai <span class="font-semibold bg-white/20 px-2 py-0.5 rounded-lg">Dokter</span>. Berikut ringkasan antrean Anda hari ini.</p>
                @else
                    <p class="text-blue-100 mt-1 text-sm">Anda login sebagai <span class="font-semibold bg-white/20 px-2 py-0.5 rounded-lg">Pasien</span>. Berikut riwayat kunjungan Anda.</p>
                @endif
            </div>
            <div class="text-5xl opacity-80">
                @if($user->isAdmin()) 🏥
                @elseif($user->isDokter()) 🩺
                @else 🧑‍⚕️
                @endif
            </div>
        </div>
    </div>

    {{-- ===== STATISTIK ADMIN ===== --}}
    @if($user->isAdmin())
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Total Pasien --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center text-2xl shrink-0">👥</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Pasien</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_pasien'] }}</p>
            </div>
        </div>
        {{-- Total Dokter Aktif --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl shrink-0">🩺</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Dokter Aktif</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_dokter'] }}</p>
            </div>
        </div>
        {{-- Antrean Aktif --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-2xl shrink-0">⏳</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Antrean Aktif</p>
                <p class="text-3xl font-bold text-amber-600">{{ $stats['antrean_aktif'] }}</p>
            </div>
        </div>
        {{-- Pendaftaran Hari Ini --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl shrink-0">📅</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Kunjungan Hari Ini</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['pendaftaran_hari_ini'] }}</p>
            </div>
        </div>
        {{-- Selesai Hari Ini --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl shrink-0">✅</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Selesai Hari Ini</p>
                <p class="text-3xl font-bold text-emerald-600">{{ $stats['selesai_hari_ini'] }}</p>
            </div>
        </div>
        {{-- Total Rekam Medis --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl shrink-0">📋</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Rekam Medis</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_rekam_medis'] }}</p>
            </div>
        </div>
    </div>

    {{-- ===== STATISTIK DOKTER ===== --}}
    @elseif($user->isDokter())
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl shrink-0">📅</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Antrean Hari Ini</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['antrean_hari_ini'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-2xl shrink-0">⏳</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Menunggu</p>
                <p class="text-3xl font-bold text-amber-600">{{ $stats['menunggu'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl shrink-0">🔄</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Sedang Diproses</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['diproses'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl shrink-0">✅</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Selesai Hari Ini</p>
                <p class="text-3xl font-bold text-emerald-600">{{ $stats['selesai_hari_ini'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center text-2xl shrink-0">👥</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Pasien Ditangani</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_pasien_ditangani'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl shrink-0">📋</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Rekam Medis Dibuat</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_rekam_medis'] }}</p>
            </div>
        </div>
    </div>

    {{-- ===== STATISTIK PASIEN ===== --}}
    @else
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl shrink-0">📅</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Kunjungan</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_kunjungan'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-2xl shrink-0">⏳</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Menunggu</p>
                <p class="text-3xl font-bold text-amber-600">{{ $stats['menunggu'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl shrink-0">🔄</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Sedang Diproses</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['diproses'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl shrink-0">✅</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Kunjungan Selesai</p>
                <p class="text-3xl font-bold text-emerald-600">{{ $stats['selesai'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl shrink-0">📋</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Rekam Medis Saya</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_rekam_medis'] }}</p>
            </div>
        </div>
        {{-- Status kunjungan terakhir --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-2xl shrink-0">🏷️</div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Status Kunjungan Terakhir</p>
                @if($stats['kunjungan_terakhir'])
                    @php $lastStatus = $stats['kunjungan_terakhir']->status; @endphp
                    <span class="inline-flex items-center mt-1 px-2.5 py-1 rounded-full text-xs font-semibold
                        {{ $lastStatus === 'selesai' ? 'bg-emerald-100 text-emerald-700' :
                           ($lastStatus === 'diproses' ? 'bg-blue-100 text-blue-700' :
                           ($lastStatus === 'batal' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')) }}">
                        {{ ucfirst($lastStatus) }}
                    </span>
                @else
                    <p class="text-sm text-gray-400 italic mt-1">Belum ada kunjungan</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ===== TABEL AKTIVITAS TERBARU ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            @if($user->isAdmin())
                <h2 class="text-base font-bold text-gray-900">Pendaftaran Terbaru</h2>
                <a href="{{ route('pendaftaran.index') }}" class="text-sm text-blue-600 hover:underline font-medium">Lihat Semua →</a>
            @elseif($user->isDokter())
                <h2 class="text-base font-bold text-gray-900">Antrean Hari Ini</h2>
                <a href="{{ route('pendaftaran.index') }}" class="text-sm text-blue-600 hover:underline font-medium">Lihat Semua →</a>
            @else
                <h2 class="text-base font-bold text-gray-900">Riwayat Kunjungan Terakhir</h2>
                <a href="{{ route('pendaftaran.index') }}" class="text-sm text-blue-600 hover:underline font-medium">Lihat Semua →</a>
            @endif
        </div>

        @if($recentPendaftaran->isNotEmpty())
        <div class="divide-y divide-gray-50">
            @foreach($recentPendaftaran as $p)
            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                        @if($user->isDokter())
                            {{ strtoupper(substr($p->pasien?->nama ?? '?', 0, 2)) }}
                        @elseif($user->isPasien())
                            {{ strtoupper(substr($p->dokter?->nama ?? '?', 0, 2)) }}
                        @else
                            {{ strtoupper(substr($p->pasien?->nama ?? '?', 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        @if($user->isDokter())
                            <p class="text-sm font-semibold text-gray-900">{{ $p->pasien?->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $p->keluhan }}</p>
                        @elseif($user->isPasien())
                            <p class="text-sm font-semibold text-gray-900">dr. {{ $p->dokter?->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $p->keluhan }}</p>
                        @else
                            <p class="text-sm font-semibold text-gray-900">{{ $p->pasien?->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-500">dr. {{ $p->dokter?->nama ?? '-' }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-right flex flex-col items-end gap-1">
                    <span class="font-mono text-xs text-gray-400">{{ $p->kode_daftar }}</span>
                    <span class="text-xs {{ $p->status === 'selesai' ? 'text-emerald-600 bg-emerald-50' :
                        ($p->status === 'diproses' ? 'text-blue-600 bg-blue-50' :
                        ($p->status === 'batal' ? 'text-rose-600 bg-rose-50' : 'text-amber-600 bg-amber-50'))
                    }} px-2 py-0.5 rounded-full font-semibold">
                        {{ ucfirst($p->status) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-8 text-center">
            <div class="text-4xl mb-3">📭</div>
            <p class="text-sm text-gray-500">Belum ada data untuk ditampilkan.</p>
        </div>
        @endif
    </div>

    {{-- Shortcut Actions --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @if($user->isAdmin() || $user->isDokter())
        <a href="{{ route('rekam-medis.create') }}"
           class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-center group">
            <span class="text-3xl group-hover:scale-110 transition-transform">📝</span>
            <span class="text-xs font-semibold text-gray-700">Input Rekam Medis</span>
        </a>
        @endif
        <a href="{{ route('pendaftaran.index') }}"
           class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-center group">
            <span class="text-3xl group-hover:scale-110 transition-transform">📅</span>
            <span class="text-xs font-semibold text-gray-700">
                @if($user->isDokter()) Antrean Saya
                @elseif($user->isPasien()) Daftar Kunjungan
                @else Pendaftaran
                @endif
            </span>
        </a>
        <a href="{{ route('rekam-medis.index') }}"
           class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-center group">
            <span class="text-3xl group-hover:scale-110 transition-transform">📋</span>
            <span class="text-xs font-semibold text-gray-700">Rekam Medis</span>
        </a>
        @if($user->isAdmin())
        <a href="{{ route('dokter.index') }}"
           class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-center group">
            <span class="text-3xl group-hover:scale-110 transition-transform">🩺</span>
            <span class="text-xs font-semibold text-gray-700">Data Dokter</span>
        </a>
        @elseif($user->isPasien())
        <a href="{{ route('pendaftaran.create') }}"
           class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-center group">
            <span class="text-3xl group-hover:scale-110 transition-transform">➕</span>
            <span class="text-xs font-semibold text-gray-700">Buat Janji</span>
        </a>
        @else
        <a href="{{ route('dokter.index') }}"
           class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all text-center group">
            <span class="text-3xl group-hover:scale-110 transition-transform">🗓️</span>
            <span class="text-xs font-semibold text-gray-700">Jadwal Dokter</span>
        </a>
        @endif
    </div>
</div>
@endsection
```

---

### 8. `resources/views/rekam-medis/index.blade.php`
```blade
@extends('layouts.app')

@section('title', 'Rekam Medis')

@section('header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Rekam Medis</span>
        </div>
        @if(auth()->user()->isAdmin())
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Manajemen Rekam Medis</h1>
            <p class="text-sm text-gray-500 mt-1">Seluruh catatan medis pasien yang telah menjalani pemeriksaan</p>
        @elseif(auth()->user()->isDokter())
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Rekam Medis Pasien Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Catatan medis pasien yang Anda tangani</p>
        @else
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Rekam Medis Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Riwayat diagnosa dan catatan medis dari dokter</p>
        @endif
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isDokter())
    <a href="{{ route('rekam-medis.create') }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Input Rekam Medis</span>
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-5">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
         class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('rekam-medis.index') }}" class="flex items-center gap-3">
        <div class="relative flex-1">
            <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari diagnosa, tindakan, nama pasien..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
        </div>
        <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
            Cari
        </button>
        @if(request('search'))
        <a href="{{ route('rekam-medis.index') }}" class="px-4 py-2.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
            Reset
        </a>
        @endif
    </form>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left py-3.5 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        @if(!auth()->user()->isPasien())
                        <th class="text-left py-3.5 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Pasien</th>
                        @endif
                        @if(!auth()->user()->isDokter())
                        <th class="text-left py-3.5 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Dokter</th>
                        @endif
                        <th class="text-left py-3.5 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Diagnosa</th>
                        <th class="text-left py-3.5 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tindakan</th>
                        <th class="text-center py-3.5 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rekamMedis as $rm)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-4 whitespace-nowrap">
                            <p class="font-semibold text-gray-800 text-sm">
                                {{ $rm->pendaftaran?->tgl_kunjungan?->format('d/m/Y') ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $rm->pendaftaran?->kode_daftar ?? '-' }}</p>
                        </td>

                        @if(!auth()->user()->isPasien())
                        <td class="py-4 px-4">
                            @if($rm->pendaftaran?->pasien)
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0
                                    {{ $rm->pendaftaran->pasien->jenis_kelamin === 'L' ? 'bg-sky-100 text-sky-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ strtoupper(substr($rm->pendaftaran->pasien->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $rm->pendaftaran->pasien->nama }}</p>
                                    <p class="text-xs text-gray-400 font-mono">NIK: {{ $rm->pendaftaran->pasien->nik }}</p>
                                </div>
                            </div>
                            @else
                            <span class="text-xs text-gray-400 italic">—</span>
                            @endif
                        </td>
                        @endif

                        @if(!auth()->user()->isDokter())
                        <td class="py-4 px-4">
                            <p class="font-medium text-gray-900 text-sm">{{ $rm->pendaftaran?->dokter?->nama ?? '-' }}</p>
                            @if($rm->pendaftaran?->dokter?->spesialisasi)
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-medium">
                                {{ $rm->pendaftaran->dokter->spesialisasi }}
                            </span>
                            @endif
                        </td>
                        @endif

                        <td class="py-4 px-4 max-w-xs">
                            <p class="text-sm text-gray-700 line-clamp-2" title="{{ $rm->diagnosa }}">
                                {{ $rm->diagnosa }}
                            </p>
                        </td>

                        <td class="py-4 px-4 max-w-xs">
                            <p class="text-xs text-gray-500 line-clamp-2">
                                {{ $rm->tindakan ?? '—' }}
                            </p>
                        </td>

                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Lihat Detail --}}
                                <a href="{{ route('rekam-medis.show', $rm) }}"
                                   class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors"
                                   title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                {{-- Edit: Dokter & Admin --}}
                                @if(auth()->user()->isAdmin() || auth()->user()->isDokter())
                                <a href="{{ route('rekam-medis.edit', $rm) }}"
                                   class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-colors"
                                   title="Edit Rekam Medis">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif

                                {{-- Hapus: Admin only --}}
                                @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('rekam-medis.destroy', $rm) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus rekam medis ini? Status pendaftaran akan dikembalikan ke diproses.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors"
                                            title="Hapus Rekam Medis">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center">
                            <div class="max-w-sm mx-auto space-y-3">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 mx-auto text-3xl">📋</div>
                                <h3 class="text-base font-semibold text-gray-900">Belum ada rekam medis</h3>
                                <p class="text-xs text-gray-500">
                                    @if(request('search'))
                                        Tidak ada rekam medis yang cocok dengan pencarian Anda.
                                    @else
                                        Rekam medis akan muncul setelah dokter melakukan input diagnosa.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rekamMedis->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $rekamMedis->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
```

---

### 9. `resources/views/rekam-medis/create.blade.php`
```blade
@extends('layouts.app')

@section('title', 'Input Rekam Medis')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('rekam-medis.index') }}" class="hover:text-blue-600 transition-colors">Rekam Medis</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Input Baru</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Form Input Rekam Medis</h1>
        <p class="text-sm text-gray-500 mt-1">Isi hasil pemeriksaan: diagnosa, tindakan, dan resep untuk pasien</p>
    </div>
    <a href="{{ route('rekam-medis.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 shadow-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Kembali</span>
    </a>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Error Summary --}}
    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl">
        <h3 class="text-sm font-semibold mb-1">Terdapat kesalahan:</h3>
        <ul class="list-disc list-inside text-sm space-y-0.5 text-rose-700">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('rekam-medis.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- PILIH PENDAFTARAN --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">1</div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Pilih Pendaftaran Pasien</h2>
                    <p class="text-xs text-gray-500">Pilih pendaftaran yang akan dibuatkan rekam medis (status: menunggu/diproses)</p>
                </div>
            </div>

            <div x-data="{ selected: '{{ old('pendaftaran_id', $selectedPendaftaran?->id ?? '') }}' }">
                <label for="pendaftaran_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Pendaftaran <span class="text-rose-500">*</span>
                </label>
                <select id="pendaftaran_id" name="pendaftaran_id"
                        x-model="selected" required
                        class="w-full px-4 py-2.5 text-sm border @error('pendaftaran_id') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                    <option value="">-- Pilih Pendaftaran --</option>
                    @foreach($pendaftarans as $pend)
                        <option value="{{ $pend->id }}" {{ old('pendaftaran_id', $selectedPendaftaran?->id) == $pend->id ? 'selected' : '' }}>
                            {{ $pend->kode_daftar }} — {{ $pend->pasien?->nama ?? 'Pasien' }}
                            ({{ $pend->tgl_kunjungan?->format('d/m/Y') }}, {{ substr($pend->jam_kunjungan, 0, 5) }})
                            — {{ $pend->status }}
                        </option>
                    @endforeach
                </select>
                @error('pendaftaran_id')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror

                @if($pendaftarans->isEmpty())
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700">
                    ⚠️ Tidak ada pendaftaran yang siap dibuatkan rekam medis.
                    Pastikan ada pendaftaran dengan status <strong>menunggu</strong> atau <strong>diproses</strong> yang belum memiliki rekam medis.
                </div>
                @endif
            </div>
        </div>

        {{-- HASIL PEMERIKSAAN --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">2</div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Hasil Pemeriksaan</h2>
                    <p class="text-xs text-gray-500">Isi diagnosa dan tindakan medis yang diberikan kepada pasien</p>
                </div>
            </div>

            {{-- Diagnosa --}}
            <div>
                <label for="diagnosa" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Diagnosa <span class="text-rose-500">*</span>
                </label>
                <textarea id="diagnosa" name="diagnosa" rows="3" required
                          placeholder="Contoh: Infeksi saluran pernapasan atas (ISPA), Demam berdarah dengue (DBD)..."
                          class="w-full px-4 py-2.5 text-sm border @error('diagnosa') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y">{{ old('diagnosa') }}</textarea>
                @error('diagnosa')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tindakan --}}
            <div>
                <label for="tindakan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Tindakan Medis
                </label>
                <textarea id="tindakan" name="tindakan" rows="3"
                          placeholder="Contoh: Pemberian infus, injeksi antibiotik, perawatan luka..."
                          class="w-full px-4 py-2.5 text-sm border @error('tindakan') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y">{{ old('tindakan') }}</textarea>
                @error('tindakan')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- RESEP & CATATAN --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">3</div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Resep Obat & Catatan</h2>
                    <p class="text-xs text-gray-500">Daftar obat yang diresepkan dan catatan tambahan dari dokter</p>
                </div>
            </div>

            {{-- Resep --}}
            <div>
                <label for="resep" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Resep Obat
                </label>
                <textarea id="resep" name="resep" rows="4"
                          placeholder="Contoh:&#10;1. Paracetamol 500mg — 3x1 sehari sesudah makan&#10;2. Amoxicillin 500mg — 3x1 sehari selama 5 hari&#10;3. Vitamin C 1000mg — 1x1 sehari"
                          class="w-full px-4 py-2.5 text-sm border @error('resep') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y">{{ old('resep') }}</textarea>
                @error('resep')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label for="catatan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Catatan Dokter
                </label>
                <textarea id="catatan" name="catatan" rows="2"
                          placeholder="Catatan tambahan, saran, atau instruksi khusus untuk pasien..."
                          class="w-full px-4 py-2.5 text-sm border @error('catatan') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Info Status --}}
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700 flex items-start gap-2">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Setelah rekam medis disimpan, status pendaftaran pasien akan <strong>otomatis diubah menjadi Selesai</strong>.</span>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3 justify-end">
            <a href="{{ route('rekam-medis.index') }}"
               class="px-5 py-2.5 text-sm font-semibold text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Rekam Medis
            </button>
        </div>
    </form>
</div>
@endsection
```

---

### 10. `resources/views/rekam-medis/edit.blade.php`
```blade
@extends('layouts.app')

@section('title', 'Edit Rekam Medis')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('rekam-medis.index') }}" class="hover:text-blue-600 transition-colors">Rekam Medis</a>
            <span>/</span>
            <a href="{{ route('rekam-medis.show', $rekamMedis) }}" class="hover:text-blue-600 transition-colors">Detail</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Edit</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Edit Rekam Medis</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui diagnosa, tindakan, atau resep pasien</p>
    </div>
    <a href="{{ route('rekam-medis.show', $rekamMedis) }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 shadow-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Kembali ke Detail</span>
    </a>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Error Summary --}}
    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl">
        <h3 class="text-sm font-semibold mb-1">Terdapat kesalahan:</h3>
        <ul class="list-disc list-inside text-sm space-y-0.5 text-rose-700">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Info Pasien & Pendaftaran (read-only) --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                {{ strtoupper(substr($rekamMedis->pendaftaran?->pasien?->nama ?? 'P', 0, 2)) }}
            </div>
            <div class="flex-1">
                <p class="font-bold text-blue-900">{{ $rekamMedis->pendaftaran?->pasien?->nama ?? '-' }}</p>
                <p class="text-xs text-blue-700 mt-0.5">NIK: {{ $rekamMedis->pendaftaran?->pasien?->nik ?? '-' }}</p>
                <div class="flex items-center gap-3 mt-2 text-xs text-blue-600 flex-wrap">
                    <span>🏷️ Kode: <strong>{{ $rekamMedis->pendaftaran?->kode_daftar ?? '-' }}</strong></span>
                    <span>📅 Tanggal: <strong>{{ $rekamMedis->pendaftaran?->tgl_kunjungan?->format('d/m/Y') ?? '-' }}</strong></span>
                    <span>🩺 Dokter: <strong>{{ $rekamMedis->pendaftaran?->dokter?->nama ?? '-' }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('rekam-medis.update', $rekamMedis) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- DIAGNOSA & TINDAKAN --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">✏️</div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Hasil Pemeriksaan</h2>
                    <p class="text-xs text-gray-500">Perbarui diagnosa dan tindakan medis</p>
                </div>
            </div>

            {{-- Diagnosa --}}
            <div>
                <label for="diagnosa" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Diagnosa <span class="text-rose-500">*</span>
                </label>
                <textarea id="diagnosa" name="diagnosa" rows="3" required
                          class="w-full px-4 py-2.5 text-sm border @error('diagnosa') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y">{{ old('diagnosa', $rekamMedis->diagnosa) }}</textarea>
                @error('diagnosa')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tindakan --}}
            <div>
                <label for="tindakan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Tindakan Medis
                </label>
                <textarea id="tindakan" name="tindakan" rows="3"
                          class="w-full px-4 py-2.5 text-sm border @error('tindakan') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y">{{ old('tindakan', $rekamMedis->tindakan) }}</textarea>
                @error('tindakan')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- RESEP & CATATAN --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-lg">💊</div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Resep Obat & Catatan</h2>
                    <p class="text-xs text-gray-500">Perbarui daftar obat dan catatan dokter</p>
                </div>
            </div>

            {{-- Resep --}}
            <div>
                <label for="resep" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Resep Obat
                </label>
                <textarea id="resep" name="resep" rows="4"
                          class="w-full px-4 py-2.5 text-sm border @error('resep') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y">{{ old('resep', $rekamMedis->resep) }}</textarea>
                @error('resep')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label for="catatan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Catatan Dokter
                </label>
                <textarea id="catatan" name="catatan" rows="2"
                          class="w-full px-4 py-2.5 text-sm border @error('catatan') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-y">{{ old('catatan', $rekamMedis->catatan) }}</textarea>
                @error('catatan')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3 justify-end">
            <a href="{{ route('rekam-medis.show', $rekamMedis) }}"
               class="px-5 py-2.5 text-sm font-semibold text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
```

---

### 11. `resources/views/rekam-medis/show.blade.php`
```blade
@extends('layouts.app')

@section('title', 'Detail Rekam Medis')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('rekam-medis.index') }}" class="hover:text-blue-600 transition-colors">Rekam Medis</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Detail</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Detail Rekam Medis</h1>
        <p class="text-sm text-gray-500 mt-1">Kode Pendaftaran: <span class="font-mono font-semibold text-blue-600">{{ $rekamMedis->pendaftaran?->kode_daftar ?? '-' }}</span></p>
    </div>
    <div class="flex items-center gap-2">
        @if(auth()->user()->isAdmin() || auth()->user()->isDokter())
        <a href="{{ route('rekam-medis.edit', $rekamMedis) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Edit</span>
        </a>
        @endif
        <a href="{{ route('rekam-medis.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
         class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    @endif

    {{-- INFO PASIEN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Informasi Pasien & Kunjungan</h2>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
            @php $pend = $rekamMedis->pendaftaran; @endphp

            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center font-bold text-sm
                    {{ $pend?->pasien?->jenis_kelamin === 'L' ? 'bg-sky-100 text-sky-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ strtoupper(substr($pend?->pasien?->nama ?? 'P', 0, 2)) }}
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Nama Pasien</p>
                    <p class="font-bold text-gray-900">{{ $pend?->pasien?->nama ?? '-' }}</p>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">NIK: {{ $pend?->pasien?->nik ?? '-' }}</p>
                </div>
            </div>

            <div>
                <p class="text-xs text-gray-400 font-medium">Dokter Pemeriksa</p>
                <p class="font-bold text-gray-900">dr. {{ $pend?->dokter?->nama ?? '-' }}</p>
                <p class="text-xs text-gray-500">{{ $pend?->dokter?->spesialisasi ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 font-medium">Kode Pendaftaran</p>
                <p class="font-mono font-bold text-blue-600">{{ $pend?->kode_daftar ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 font-medium">Tanggal Kunjungan</p>
                <p class="font-semibold text-gray-900">{{ $pend?->tgl_kunjungan?->format('d F Y') ?? '-' }}</p>
                <p class="text-xs text-gray-500">Pukul {{ substr($pend?->jam_kunjungan ?? '', 0, 5) }} WIB</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 font-medium">Keluhan Utama</p>
                <p class="text-sm text-gray-700">{{ $pend?->keluhan ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-400 font-medium">Status Kunjungan</p>
                <span class="inline-flex items-center mt-1 px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ ($pend?->status ?? '') === 'selesai' ? 'bg-emerald-100 text-emerald-700' :
                       (($pend?->status ?? '') === 'diproses' ? 'bg-blue-100 text-blue-700' :
                       'bg-amber-100 text-amber-700') }}">
                    {{ ucfirst($pend?->status ?? '-') }}
                </span>
            </div>
        </div>
    </div>

    {{-- REKAM MEDIS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Catatan Medis</h2>
        </div>
        <div class="p-6 space-y-6">

            {{-- Diagnosa --}}
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">🔬 Diagnosa</p>
                <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl text-sm text-gray-800 whitespace-pre-line leading-relaxed">
                    {{ $rekamMedis->diagnosa ?? '-' }}
                </div>
            </div>

            {{-- Tindakan --}}
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">🏥 Tindakan Medis</p>
                @if($rekamMedis->tindakan)
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-gray-800 whitespace-pre-line leading-relaxed">
                    {{ $rekamMedis->tindakan }}
                </div>
                @else
                <p class="text-sm text-gray-400 italic">Tidak ada tindakan medis yang dicatat.</p>
                @endif
            </div>

            {{-- Resep --}}
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">💊 Resep Obat</p>
                @if($rekamMedis->resep)
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-sm text-gray-800 whitespace-pre-line leading-relaxed font-mono">
                    {{ $rekamMedis->resep }}
                </div>
                @else
                <p class="text-sm text-gray-400 italic">Tidak ada resep obat.</p>
                @endif
            </div>

            {{-- Catatan --}}
            @if($rekamMedis->catatan)
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">📝 Catatan Dokter</p>
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-sm text-gray-800 whitespace-pre-line leading-relaxed">
                    {{ $rekamMedis->catatan }}
                </div>
            </div>
            @endif

            {{-- Metadata --}}
            <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                <span>Dibuat: {{ $rekamMedis->created_at?->format('d/m/Y H:i') ?? '-' }}</span>
                <span>Diperbarui: {{ $rekamMedis->updated_at?->format('d/m/Y H:i') ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Action footer --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('rekam-medis.index') }}"
           class="text-sm text-gray-500 hover:text-gray-800 font-medium transition-colors">
            ← Kembali ke Daftar
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isDokter())
        <a href="{{ route('rekam-medis.edit', $rekamMedis) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Rekam Medis
        </a>
        @endif
    </div>
</div>
@endsection


