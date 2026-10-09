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