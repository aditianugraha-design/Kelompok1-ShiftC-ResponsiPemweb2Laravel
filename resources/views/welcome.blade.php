<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Klinik Sehat - Pelayanan Cepat & Terpercaya</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">

{{-- NAVBAR --}}
<nav class="bg-white border-b border-gray-200 h-16 flex items-center px-4 lg:px-8 sticky top-0 z-50 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </div>
        <span class="font-bold text-lg text-gray-800">Klinik Sehat</span>
    </div>

    <div class="ml-auto flex items-center gap-2">
        @auth
            <a href="{{ url('/dashboard') }}"
               class="text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-xl transition">
                Dashboard
            </a>
        @else
            <a href="{{ route('login') }}"
               class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2 rounded-xl transition">
                Masuk
            </a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-xl transition">
                    Daftar
                </a>
            @endif
        @endauth
    </div>
</nav>

{{-- HERO --}}
<div class="max-w-6xl mx-auto px-4 lg:px-8 mt-6">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 lg:p-10 text-white shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center gap-8">
            <div class="flex-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/20 text-xs font-semibold">
                    🏥 Pelayanan Kesehatan Terpadu
                </span>
                <h1 class="mt-4 text-3xl lg:text-4xl font-bold leading-tight">Sehat dimulai dari kemudahan berobat.</h1>
                <p class="mt-3 text-blue-100 text-sm lg:text-base max-w-lg">
                    Daftar kunjungan secara online, pantau status antrean, dan akses rekam medis Anda —
                    semua dalam satu aplikasi Klinik Sehat.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-white text-blue-700 font-semibold text-sm rounded-xl hover:bg-blue-50 transition">
                            Buka Dashboard →
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-white text-blue-700 font-semibold text-sm rounded-xl hover:bg-blue-50 transition">
                            Daftar Sekarang →
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-white/15 text-white font-semibold text-sm rounded-xl hover:bg-white/25 transition border border-white/30">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
            <div class="text-7xl lg:text-8xl opacity-90 select-none">🩺</div>
        </div>
    </div>
</div>

{{-- FITUR --}}
<div class="max-w-6xl mx-auto px-4 lg:px-8 mt-6">
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl">📅</div>
            <h3 class="mt-3 font-bold text-gray-900">Pendaftaran Online</h3>
            <p class="mt-1 text-sm text-gray-500">Pilih dokter dan jadwal kunjungan tanpa antre di loket.</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl">🩺</div>
            <h3 class="mt-3 font-bold text-gray-900">Dokter Profesional</h3>
            <p class="mt-1 text-sm text-gray-500">Ditangani dokter berpengalaman sesuai kebutuhan Anda.</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl">📋</div>
            <h3 class="mt-3 font-bold text-gray-900">Rekam Medis Digital</h3>
            <p class="mt-1 text-sm text-gray-500">Riwayat diagnosis dan tindakan tersimpan rapi & aman.</p>
        </div>
    </div>
</div>

{{-- ALUR --}}
<div class="max-w-6xl mx-auto px-4 lg:px-8 mt-4 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-900">Cara Menggunakan Layanan</h2>
        <div class="mt-4 grid sm:grid-cols-3 gap-4 text-sm">
            <div class="flex items-start gap-3">
                <span class="w-8 h-8 shrink-0 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center">1</span>
                <p class="text-gray-600"><span class="font-semibold text-gray-900">Daftar akun pasien</span><br>Isi data diri sekali saja.</p>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-8 h-8 shrink-0 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center">2</span>
                <p class="text-gray-600"><span class="font-semibold text-gray-900">Buat pendaftaran</span><br>Pilih dokter & tanggal kunjungan.</p>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-8 h-8 shrink-0 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center">3</span>
                <p class="text-gray-600"><span class="font-semibold text-gray-900">Datang & periksa</span><br>Pantau status antrean Anda.</p>
            </div>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} Klinik Sehat. v{{ app()->version() }}</p>
</div>

</body>
</html>
