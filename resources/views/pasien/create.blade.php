@extends('layouts.app')

@section('title', 'Tambah Pasien Baru')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('pasien.index') }}" class="hover:text-blue-600 transition-colors">Pasien</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Tambah Baru</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Pendaftaran Pasien Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Lengkapi data identitas, kontak, dan riwayat dasar untuk membuat rekam medis</p>
    </div>
    <div>
        <a href="{{ route('pasien.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div x-data="createPatientForm()" class="max-w-5xl mx-auto space-y-6">

    {{-- Error Summary Alert --}}
    @if(isset($errors) && $errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm">
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

    <form action="{{ route('pasien.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Form Input Utama (2 spans) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARD 1: IDENTITAS PRIBADI --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            1
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Identitas Diri Pasien</h2>
                            <p class="text-xs text-gray-500">Nomor identitas kependudukan dan informasi personal</p>
                        </div>
                    </div>

                    {{-- NIK --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="nik" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-xs font-mono"
                                  :class="nik.length === 16 ? 'text-emerald-600 font-bold' : (nik.length > 16 ? 'text-rose-600 font-bold' : 'text-gray-400')"
                                  x-text="nik.length + ' / 16 digit'">
                            </span>
                        </div>
                        <div class="relative">
                            <input type="text"
                                   id="nik"
                                   name="nik"
                                   x-model="nik"
                                   maxlength="16"
                                   required
                                   value="{{ old('nik') }}"
                                   placeholder="Contoh: 3201012345678901"
                                   class="w-full px-4 py-2.5 text-sm border @error('nik') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono tracking-wider transition-colors">
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none" x-show="nik.length === 16">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        @error('nik')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Harus 16 digit angka dan belum pernah terdaftar sebelumnya</p>
                        @enderror
                    </div>

                    {{-- NAMA LENGKAP --}}
                    <div>
                        <label for="nama" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap Pasien <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               id="nama"
                               name="nama"
                               required
                               value="{{ old('nama') }}"
                               placeholder="Nama lengkap sesuai KTP / Akta Kelahiran"
                               class="w-full px-4 py-2.5 text-sm border @error('nama') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('nama')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- TANGGAL LAHIR & USIA --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tgl_lahir" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Tanggal Lahir <span class="text-rose-500">*</span>
                            </label>
                            <input type="date"
                                   id="tgl_lahir"
                                   name="tgl_lahir"
                                   x-model="tglLahir"
                                   required
                                   value="{{ old('tgl_lahir') }}"
                                   class="w-full px-4 py-2.5 text-sm border @error('tgl_lahir') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('tgl_lahir')
                                <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">
                                Perkiraan Usia
                            </label>
                            <div class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 flex items-center justify-between">
                                <span x-text="calculatedAge !== null ? calculatedAge + ' Tahun' : '-'"></span>
                                <span class="text-xs font-normal text-gray-400" x-show="calculatedAge !== null">Dihitung otomatis</span>
                            </div>
                        </div>
                    </div>

                    {{-- JENIS KELAMIN --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 p-3.5 border-2 rounded-2xl cursor-pointer transition-all duration-150"
                                   :class="gender === 'L' ? 'border-sky-500 bg-sky-50/60 shadow-sm' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50/50'">
                                <input type="radio"
                                       name="jenis_kelamin"
                                       value="L"
                                       x-model="gender"
                                       {{ old('jenis_kelamin', 'L') == 'L' ? 'checked' : '' }}
                                       required
                                       class="w-4 h-4 text-sky-600 focus:ring-sky-500">
                                <div>
                                    <p class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                                        <span class="text-sky-600 text-base">♂</span> Laki-laki
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pasien Pria</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3.5 border-2 rounded-2xl cursor-pointer transition-all duration-150"
                                   :class="gender === 'P' ? 'border-rose-500 bg-rose-50/60 shadow-sm' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50/50'">
                                <input type="radio"
                                       name="jenis_kelamin"
                                       value="P"
                                       x-model="gender"
                                       {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }}
                                       required
                                       class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                                <div>
                                    <p class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
                                        <span class="text-rose-600 text-base">♀</span> Perempuan
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pasien Wanita</p>
                                </div>
                            </label>
                        </div>
                        @error('jenis_kelamin')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- GOLONGAN DARAH --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                            Golongan Darah (Opsional)
                        </label>
                        <div class="grid grid-cols-5 gap-2.5">
                            @foreach(['A', 'B', 'AB', 'O'] as $goldar)
                            <label class="flex flex-col items-center justify-center p-3 border-2 rounded-xl cursor-pointer text-center transition-all"
                                   :class="golDarah === '{{ $goldar }}' ? 'border-blue-600 bg-blue-50/60 text-blue-700 font-bold' : 'border-gray-200 hover:border-gray-300 text-gray-700 font-medium'">
                                <input type="radio"
                                       name="golongan_darah"
                                       value="{{ $goldar }}"
                                       x-model="golDarah"
                                       {{ old('golongan_darah') == $goldar ? 'checked' : '' }}
                                       class="sr-only">
                                <span class="text-base">{{ $goldar }}</span>
                            </label>
                            @endforeach

                            <label class="flex flex-col items-center justify-center p-3 border-2 rounded-xl cursor-pointer text-center transition-all"
                                   :class="golDarah === '' ? 'border-gray-400 bg-gray-100 text-gray-700 font-bold' : 'border-gray-200 hover:border-gray-300 text-gray-400'">
                                <input type="radio"
                                       name="golongan_darah"
                                       value=""
                                       x-model="golDarah"
                                       {{ old('golongan_darah') == '' ? 'checked' : '' }}
                                       class="sr-only">
                                <span class="text-xs">Tidak Tahu</span>
                            </label>
                        </div>
                        @error('golongan_darah')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- CARD 2: KONTAK & ALAMAT --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            2
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Kontak & Domisili</h2>
                            <p class="text-xs text-gray-500">Nomor telepon aktif dan alamat tempat tinggal</p>
                        </div>
                    </div>

                    {{-- NO TELEPON --}}
                    <div>
                        <label for="no_telp" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <input type="tel"
                                   id="no_telp"
                                   name="no_telp"
                                   required
                                   maxlength="15"
                                   value="{{ old('no_telp') }}"
                                   placeholder="Contoh: 081234567890"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border @error('no_telp') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                        @error('no_telp')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Digunakan untuk konfirmasi pendaftaran & riwayat rekam medis</p>
                        @enderror
                    </div>

                    {{-- ALAMAT LENGKAP --}}
                    <div>
                        <label for="alamat" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Alamat Lengkap Domisili <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alamat"
                                  name="alamat"
                                  rows="3"
                                  required
                                  placeholder="Nama jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten"
                                  class="w-full px-4 py-2.5 text-sm border @error('alamat') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            {{-- Kolom Kanan: Panduan & Aksi Submit (1 span) --}}
            <div class="space-y-6">

                {{-- CARD SUBMIT ACTION --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-4 sticky top-24">
                    <h3 class="text-sm font-bold text-gray-900 pb-3 border-b border-gray-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Konfirmasi Simpan</span>
                    </h3>

                    <p class="text-xs text-gray-500 leading-relaxed">
                        Pastikan seluruh data yang dimasukkan telah sesuai dengan kartu identitas resmi pasien sebelum menyimpan.
                    </p>

                    <div class="space-y-2.5 pt-2">
                        <button type="submit"
                                class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Daftarkan Pasien Baru</span>
                        </button>

                        <a href="{{ route('pasien.index') }}"
                           class="w-full py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors flex items-center justify-center">
                            Batal
                        </a>
                    </div>

                    {{-- Panduan Pendaftaran Info --}}
                    <div class="mt-6 p-4 bg-slate-50 border border-slate-200/80 rounded-xl space-y-2 text-xs text-gray-600">
                        <p class="font-bold text-gray-800 flex items-center gap-1.5">
                            <span>📌</span> Panduan Pendaftaran
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-gray-500">
                            <li>NIK wajib 16 digit dan unik.</li>
                            <li>Tanda bintang (<span class="text-rose-500">*</span>) wajib diisi.</li>
                            <li>Data tersimpan dapat diubah sewaktu-waktu melalui halaman daftar pasien.</li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<script>
function createPatientForm() {
    return {
        nik: '{{ old('nik', '') }}',
        gender: '{{ old('jenis_kelamin', 'L') }}',
        tglLahir: '{{ old('tgl_lahir', '') }}',
        golDarah: '{{ old('golongan_darah', '') }}',

        get calculatedAge() {
            if (!this.tglLahir) return null;
            try {
                const birth = new Date(this.tglLahir);
                const now = new Date();
                let age = now.getFullYear() - birth.getFullYear();
                const m = now.getMonth() - birth.getMonth();
                if (m < 0 || (m === 0 && now.getDate() < birth.getDate())) {
                    age--;
                }
                return age >= 0 ? age : null;
            } catch (e) {
                return null;
            }
        }
    };
}
</script>
@endsection
