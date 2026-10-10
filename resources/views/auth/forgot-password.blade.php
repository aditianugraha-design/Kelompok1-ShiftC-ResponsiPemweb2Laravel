<x-guest-layout>
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-xl">🔑</div>
            <div>
                <h1 class="font-bold text-gray-900">Lupa Kata Sandi?</h1>
                <p class="text-xs text-gray-500">Kami kirimkan tautan reset ke email Anda.</p>
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500">
            Masukkan alamat email terdaftar, kami akan mengirimkan tautan untuk membuat kata sandi baru.
        </p>

        <x-auth-session-status class="mt-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="mt-4 space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                    placeholder="nama@email.com" class="block mt-1 w-full rounded-xl" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center !py-3 !rounded-xl !text-sm">
                Kirim Tautan Reset
            </x-primary-button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Ingat kata sandi?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">Kembali masuk</a>
        </p>
    </div>
</x-guest-layout>
