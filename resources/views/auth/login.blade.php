<x-guest-layout>
    <div class="w-full max-w-4xl grid lg:grid-cols-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- PANEL BRANDING --}}
        <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-blue-600 to-indigo-600 p-8 text-white">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-lg leading-tight">Klinik Sehat</p>
                        <p class="text-blue-100 text-xs">Pelayanan cepat & terpercaya</p>
                    </div>
                </div>

                <h2 class="mt-8 text-2xl font-bold leading-snug">Kelola kunjungan &amp; rekam medis dalam satu tempat.</h2>
                <p class="mt-2 text-sm text-blue-100">Masuk untuk mendaftar kunjungan, melihat antrean, dan memantau riwayat kesehatan Anda.</p>

                <ul class="mt-6 space-y-3 text-sm">
                    <li class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3">
                        <span class="text-lg">📅</span>
                        <span><span class="font-semibold">Pendaftaran online</span> — tanpa antre panjang di loket.</span>
                    </li>
                    <li class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3">
                        <span class="text-lg">📋</span>
                        <span><span class="font-semibold">Rekam medis digital</span> — riwayat selalu tersimpan rapi.</span>
                    </li>
                    <li class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3">
                        <span class="text-lg">🩺</span>
                        <span><span class="font-semibold">Dokter terpercaya</span> — ditangani tenaga profesional.</span>
                    </li>
                </ul>
            </div>

            <p class="text-xs text-blue-100 mt-8">© {{ date('Y') }} Klinik Sehat. Jaga kesehatan, mulai dari sini.</p>
        </div>

        {{-- FORM --}}
        <div class="p-6 sm:p-8">
            <div class="lg:hidden flex items-center gap-3 mb-6">
                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="font-bold text-lg text-gray-800">Klinik Sehat</span>
            </div>

            <h1 class="text-2xl font-bold text-gray-900">Selamat Datang Kembali</h1>
            <p class="text-sm text-gray-500 mt-1">Masuk ke akun Anda untuk melanjutkan.</p>

            <x-auth-session-status class="mt-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Alamat Email')" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                        autocomplete="username" placeholder="nama@email.com"
                        class="block mt-1 w-full rounded-xl" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Kata Sandi')" />
                    <x-text-input id="password" type="password" name="password" required
                        autocomplete="current-password" placeholder="••••••••"
                        class="block mt-1 w-full rounded-xl" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                            Lupa kata sandi?
                        </a>
                    @endif
                </div>

                <x-primary-button class="w-full justify-center !py-3 !rounded-xl !text-sm">
                    Masuk
                </x-primary-button>
            </form>

            @if (Route::has('register'))
                <p class="mt-6 text-center text-sm text-gray-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                        Daftar sebagai pasien
                    </a>
                </p>
            @endif
        </div>
    </div>
</x-guest-layout>
