<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 pt-4 border-t border-gray-200">
            <p class="text-sm font-semibold text-gray-800">Data Identitas Pasien</p>
            <p class="text-xs text-gray-500 mt-0.5">Digunakan untuk pendaftaran kunjungan & rekam medis</p>
        </div>

        <!-- NIK -->
        <div class="mt-4">
            <x-input-label for="nik" :value="__('NIK (16 digit)')" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')" required maxlength="16" placeholder="3201012345678901" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <!-- Tanggal Lahir -->
        <div class="mt-4">
            <x-input-label for="tgl_lahir" :value="__('Tanggal Lahir')" />
            <x-text-input id="tgl_lahir" class="block mt-1 w-full" type="date" name="tgl_lahir" :value="old('tgl_lahir')" required />
            <x-input-error :messages="$errors->get('tgl_lahir')" class="mt-2" />
        </div>

        <!-- Jenis Kelamin -->
        <div class="mt-4">
            <x-input-label :value="__('Jenis Kelamin')" />
            <div class="mt-1 flex items-center gap-4">
                <label class="inline-flex items-center gap-1.5 text-sm text-gray-700">
                    <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required class="text-indigo-600 focus:ring-indigo-500">
                    <span>Laki-laki</span>
                </label>
                <label class="inline-flex items-center gap-1.5 text-sm text-gray-700">
                    <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} required class="text-indigo-600 focus:ring-indigo-500">
                    <span>Perempuan</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
        </div>

        <!-- Golongan Darah -->
        <div class="mt-4">
            <x-input-label for="golongan_darah" :value="__('Golongan Darah (Opsional)')" />
            <select id="golongan_darah" name="golongan_darah"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                <option value="">Pilih golongan darah</option>
                @foreach(['A', 'B', 'AB', 'O'] as $goldar)
                    <option value="{{ $goldar }}" {{ old('golongan_darah') == $goldar ? 'selected' : '' }}>{{ $goldar }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('golongan_darah')" class="mt-2" />
        </div>

        <!-- No. Telepon -->
        <div class="mt-4">
            <x-input-label for="no_telp" :value="__('No. Telepon / WhatsApp')" />
            <x-text-input id="no_telp" class="block mt-1 w-full" type="tel" name="no_telp" :value="old('no_telp')" required maxlength="15" placeholder="081234567890" />
            <x-input-error :messages="$errors->get('no_telp')" class="mt-2" />
        </div>

        <!-- Alamat -->
        <div class="mt-4">
            <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
            <textarea id="alamat" name="alamat" rows="3" required
                      class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                      placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota">{{ old('alamat') }}</textarea>
            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
