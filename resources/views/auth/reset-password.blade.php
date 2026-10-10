<x-guest-layout>
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-xl">🔒</div>
            <div>
                <h1 class="font-bold text-gray-900">Buat Kata Sandi Baru</h1>
                <p class="text-xs text-gray-500">Gunakan kombinasi yang kuat &amp; mudah diingat.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus
                    autocomplete="username" class="block mt-1 w-full rounded-xl" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Kata Sandi Baru')" />
                <x-text-input id="password" type="password" name="password" required
                    autocomplete="new-password" placeholder="Min. 8 karakter" class="block mt-1 w-full rounded-xl" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password" placeholder="Ulangi kata sandi" class="block mt-1 w-full rounded-xl" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center !py-3 !rounded-xl !text-sm">
                Simpan Kata Sandi
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
