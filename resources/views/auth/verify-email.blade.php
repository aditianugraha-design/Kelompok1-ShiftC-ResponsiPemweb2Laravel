<x-guest-layout>
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-xl">✉️</div>
            <div>
                <h1 class="font-bold text-gray-900">Verifikasi Email</h1>
                <p class="text-xs text-gray-500">Satu langkah lagi untuk mengaktifkan akun.</p>
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500">
            Terima kasih telah mendaftar! Silakan verifikasi alamat email dengan mengeklik tautan yang telah kami kirim. Jika belum menerima email, kami dapat mengirim ulang.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-100 text-sm font-medium text-emerald-700">
                Tautan verifikasi baru telah dikirim ke email Anda.
            </div>
        @endif

        <div class="mt-6 flex items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button class="!py-2.5 !rounded-xl !text-sm">
                    Kirim Ulang Email
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="underline text-sm text-gray-500 hover:text-gray-900 rounded-md">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
