<x-guest-layout>
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-xl">🛡️</div>
            <div>
                <h1 class="font-bold text-gray-900">Konfirmasi Kata Sandi</h1>
                <p class="text-xs text-gray-500">Area aman — verifikasi identitas Anda.</p>
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500">
            Silakan masukkan kata sandi Anda untuk melanjutkan.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="mt-4 space-y-4">
            @csrf

            <div>
                <x-input-label for="password" :value="__('Kata Sandi')" />
                <x-text-input id="password" type="password" name="password" required
                    autocomplete="current-password" placeholder="••••••••" class="block mt-1 w-full rounded-xl" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center !py-3 !rounded-xl !text-sm">
                Konfirmasi
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
