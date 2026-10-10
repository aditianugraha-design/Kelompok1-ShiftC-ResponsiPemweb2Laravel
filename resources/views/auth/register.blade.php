<x-guest-layout>
    <div class="w-full max-w-5xl grid lg:grid-cols-[1fr_1.5fr] bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

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
                        <p class="text-blue-100 text-xs">Daftar sekali, mudah berkunjung</p>
                    </div>
                </div>

                <h2 class="mt-8 text-2xl font-bold leading-snug">Buat akun pasien dalam hitungan menit.</h2>
                <p class="mt-2 text-sm text-blue-100">Satu akun untuk daftar kunjungan, pantau antrean, dan akses rekam medis Anda.</p>

                <ol class="mt-6 space-y-3 text-sm">
                    <li class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3">
                        <span class="w-7 h-7 shrink-0 rounded-full bg-white text-blue-700 text-xs font-bold flex items-center justify-center">1</span>
                        <span>Isi data akun &amp; identitas di formulir.</span>
                    </li>
                    <li class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3">
                        <span class="w-7 h-7 shrink-0 rounded-full bg-white text-blue-700 text-xs font-bold flex items-center justify-center">2</span>
                        <span>Masuk, lalu buat pendaftaran kunjungan.</span>
                    </li>
                    <li class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-3">
                        <span class="w-7 h-7 shrink-0 rounded-full bg-white text-blue-700 text-xs font-bold flex items-center justify-center">3</span>
                        <span>Datang sesuai jadwal &amp; pantau statusnya.</span>
                    </li>
                </ol>
            </div>

            <p class="text-xs text-blue-100 mt-8">Data Anda tersimpan aman dan hanya digunakan untuk layanan klinik.</p>
        </div>

        {{-- FORM --}}
        <div class="p-6 sm:p-8 max-h-[85vh] overflow-y-auto">
            <h1 class="text-2xl font-bold text-gray-900">Daftar Akun Pasien</h1>
            <p class="text-sm text-gray-500 mt-1">Lengkapi data di bawah untuk membuat akun.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-6">
                @csrf

                {{-- Bagian Akun --}}
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center">1</span>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Informasi Akun</h2>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus
                            autocomplete="name" placeholder="Sesuai KTP"
                            class="block mt-1 w-full rounded-xl" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="email" :value="__('Alamat Email')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required
                            autocomplete="username" placeholder="nama@email.com"
                            class="block mt-1 w-full rounded-xl" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Kata Sandi')" />
                        <x-text-input id="password" type="password" name="password" required
                            autocomplete="new-password" placeholder="Min. 8 karakter"
                            class="block mt-1 w-full rounded-xl" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                        <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password" placeholder="Ulangi kata sandi"
                            class="block mt-1 w-full rounded-xl" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                {{-- Bagian Identitas --}}
                <div class="flex items-center gap-2 mt-8 mb-4">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center">2</span>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Data Identitas Pasien</h2>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>
                <p class="text-xs text-gray-500 -mt-2 mb-4">Digunakan untuk pendaftaran kunjungan &amp; rekam medis.</p>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="nik" :value="__('NIK (16 digit)')" />
                        <x-text-input id="nik" type="text" name="nik" :value="old('nik')" required
                            maxlength="16" placeholder="3201012345678901"
                            class="block mt-1 w-full rounded-xl" />
                        <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="tgl_lahir" :value="__('Tanggal Lahir')" />
                        <x-text-input id="tgl_lahir" type="date" name="tgl_lahir" :value="old('tgl_lahir')" required
                            class="block mt-1 w-full rounded-xl" />
                        <x-input-error :messages="$errors->get('tgl_lahir')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Jenis Kelamin')" />
                        <div class="mt-2 flex items-center gap-3">
                            <label class="flex-1 inline-flex items-center justify-center gap-2 text-sm text-gray-700 border border-gray-300 rounded-xl px-3 py-2.5 cursor-pointer has-checked:border-blue-500 has-checked:bg-blue-50 has-checked:text-blue-700 transition">
                                <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required class="text-blue-600 focus:ring-blue-500">
                                <span class="font-medium">Laki-laki</span>
                            </label>
                            <label class="flex-1 inline-flex items-center justify-center gap-2 text-sm text-gray-700 border border-gray-300 rounded-xl px-3 py-2.5 cursor-pointer has-checked:border-blue-500 has-checked:bg-blue-50 has-checked:text-blue-700 transition">
                                <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} required class="text-blue-600 focus:ring-blue-500">
                                <span class="font-medium">Perempuan</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="golongan_darah" :value="__('Golongan Darah (Opsional)')" />
                        <select id="golongan_darah" name="golongan_darah"
                                class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm mt-1 block w-full">
                            <option value="">Pilih golongan darah</option>
                            @foreach (['A', 'B', 'AB', 'O'] as $goldar)
                                <option value="{{ $goldar }}" {{ old('golongan_darah') == $goldar ? 'selected' : '' }}>{{ $goldar }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('golongan_darah')" class="mt-2" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="no_telp" :value="__('No. Telepon / WhatsApp')" />
                        <x-text-input id="no_telp" type="tel" name="no_telp" :value="old('no_telp')" required
                            maxlength="15" placeholder="081234567890"
                            class="block mt-1 w-full rounded-xl" />
                        <x-input-error :messages="$errors->get('no_telp')" class="mt-2" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                        <textarea id="alamat" name="alamat" rows="3" required
                                  class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm block mt-1 w-full"
                                  placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota">{{ old('alamat') }}</textarea>
                        <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                    </div>
                </div>

                <x-primary-button class="w-full justify-center !py-3 !rounded-xl !text-sm mt-6">
                    Buat Akun
                </x-primary-button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>
