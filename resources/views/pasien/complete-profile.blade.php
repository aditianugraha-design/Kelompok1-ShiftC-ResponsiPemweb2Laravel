@extends('layouts.app')

@section('title', 'Lengkapi Profil Pasien')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Lengkapi Profil</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Lengkapi Profil Pasien</h1>
        <p class="text-sm text-gray-500 mt-1">Data identitas ini diperlukan untuk mendaftar kunjungan dan membuat rekam medis</p>
    </div>
    <div>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- INFO BANNER --}}
    <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl">
        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="text-sm">
            <p class="font-semibold">Profil Anda belum lengkap</p>
            <p class="text-blue-700 text-xs mt-0.5">Silakan lengkapi data identitas di bawah ini. Pastikan NIK dan tanggal lahir sesuai dengan kartu identitas resmi.</p>
        </div>
    </div>

    {{-- ERROR SUMMARY --}}
    @if(isset($errors) && $errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-sm">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold">Terdapat kesalahan pada input formulir:</h3>
                <ul class="mt-1 list-disc list-inside text-sm space-y-0.5 text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('pasien.store-complete-profile') }}" method="POST" class="space-y-6">
        @csrf

        {{-- CARD: IDENTITAS DIRI --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">1</div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Identitas Diri Pasien</h2>
                    <p class="text-xs text-gray-500">Nomor identitas kependudukan dan informasi personal</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- NIK --}}
                <div class="sm:col-span-2">
                    <label for="nik" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nik" name="nik" maxlength="16" required
                           value="{{ old('nik', $pasien?->nik) }}"
                           placeholder="16 digit nomor NIK KTP"
                           class="w-full px-4 py-2.5 text-sm border @error('nik') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono tracking-wider transition-colors">
                    @error('nik')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @else
                        <p class="text-xs text-gray-400 mt-1">Harus 16 digit angka dan belum pernah terdaftar</p>
                    @enderror
                </div>

                {{-- Nama --}}
                <div class="sm:col-span-2">
                    <label for="nama" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap Pasien <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" required
                           value="{{ old('nama', $pasien?->nama ?? auth()->user()->name) }}"
                           placeholder="Nama lengkap sesuai KTP"
                           class="w-full px-4 py-2.5 text-sm border @error('nama') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('nama')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Lahir --}}
                <div>
                    <label for="tgl_lahir" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tgl_lahir" name="tgl_lahir" required
                           value="{{ old('tgl_lahir', $pasien?->tgl_lahir?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 text-sm border @error('tgl_lahir') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    @error('tgl_lahir')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Golongan Darah --}}
                <div>
                    <label for="golongan_darah" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Golongan Darah (Opsional)
                    </label>
                    <select id="golongan_darah" name="golongan_darah"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                        <option value="">Pilih golongan darah</option>
                        @foreach(['A', 'B', 'AB', 'O'] as $goldar)
                            <option value="{{ $goldar }}" {{ old('golongan_darah', $pasien?->golongan_darah) == $goldar ? 'selected' : '' }}>{{ $goldar }}</option>
                        @endforeach
                    </select>
                    @error('golongan_darah')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Kelamin --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 p-3 border-2 rounded-xl cursor-pointer hover:bg-gray-50 {{ old('jenis_kelamin', $pasien?->jenis_kelamin) == 'L' ? 'border-sky-500 bg-sky-50/60' : 'border-gray-200' }}">
                            <input type="radio" name="jenis_kelamin" value="L"
                                   {{ old('jenis_kelamin', $pasien?->jenis_kelamin ?? 'L') == 'L' ? 'checked' : '' }}
                                   required class="w-4 h-4 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm font-semibold text-gray-800">♂ Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 border-2 rounded-xl cursor-pointer hover:bg-gray-50 {{ old('jenis_kelamin', $pasien?->jenis_kelamin) == 'P' ? 'border-rose-500 bg-rose-50/60' : 'border-gray-200' }}">
                            <input type="radio" name="jenis_kelamin" value="P"
                                   {{ old('jenis_kelamin', $pasien?->jenis_kelamin) == 'P' ? 'checked' : '' }}
                                   required class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                            <span class="text-sm font-semibold text-gray-800">♀ Perempuan</span>
                        </label>
                    </div>
                    @error('jenis_kelamin')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- CARD: KONTAK & ALAMAT --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">2</div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Kontak & Domisili</h2>
                    <p class="text-xs text-gray-500">Nomor telepon aktif dan alamat tempat tinggal</p>
                </div>
            </div>

            <div>
                <label for="no_telp" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                </label>
                <input type="tel" id="no_telp" name="no_telp" maxlength="15" required
                       value="{{ old('no_telp', $pasien?->no_telp ?? auth()->user()->no_telp) }}"
                       placeholder="Contoh: 081234567890"
                       class="w-full px-4 py-2.5 text-sm border @error('no_telp') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                @error('no_telp')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="alamat" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                    Alamat Lengkap Domisili <span class="text-rose-500">*</span>
                </label>
                <textarea id="alamat" name="alamat" rows="3" required
                          placeholder="Nama jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten"
                          class="w-full px-4 py-2.5 text-sm border @error('alamat') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('alamat', $pasien?->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- SUBMIT --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard') }}"
               class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors">
                Nanti Saja
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-500/20 transition-all inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Simpan Profil</span>
            </button>
        </div>
    </form>
</div>
@endsection
