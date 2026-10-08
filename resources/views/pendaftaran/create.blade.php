@extends('layouts.app')

@section('title', 'Tambah Pendaftaran Baru')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('pendaftaran.index') }}" class="hover:text-blue-600 transition-colors">Pendaftaran</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Tambah Baru</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Form Pendaftaran Kunjungan</h1>
        <p class="text-sm text-gray-500 mt-1">Lengkapi data pasien, dokter pemeriksa, dan waktu kedatangan untuk nomor antrean klinik</p>
    </div>
    <div>
        <a href="{{ route('pendaftaran.index') }}"
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
<div x-data="createPendaftaranForm()" class="max-w-5xl mx-auto space-y-6">

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

    <form action="{{ route('pendaftaran.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Form Input Utama (2 spans) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARD 1: PASIEN & DOKTER --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            1
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Pasien & Dokter Pemeriksa</h2>
                            <p class="text-xs text-gray-500">Tentukan pasien yang berobat dan dokter yang menangani</p>
                        </div>
                    </div>

                    {{-- PILIH PASIEN --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="pasien_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Pasien <span class="text-rose-500">*</span>
                            </label>
                            <a href="{{ route('pasien.create') }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                                + Pasien Belum Terdaftar?
                            </a>
                        </div>
                        <select id="pasien_id"
                                name="pasien_id"
                                x-model="selectedPasienId"
                                @change="updatePasienName($event)"
                                required
                                class="w-full px-4 py-2.5 text-sm border @error('pasien_id') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                            <option value="">-- Pilih Pasien Terdaftar --</option>
                            @foreach($pasiens as $p)
                                <option value="{{ $p->id }}"
                                        data-nama="{{ $p->nama }}"
                                        data-nik="{{ $p->nik }}"
                                        data-telepon="{{ $p->no_telp }}"
                                        {{ old('pasien_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} — NIK: {{ $p->nik }} ({{ $p->no_telp }})
                                </option>
                            @endforeach
                        </select>
                        @error('pasien_id')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PILIH DOKTER --}}
                    <div>
                        <label for="dokter_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Dokter Pemeriksa <span class="text-rose-500">*</span>
                        </label>
                        <select id="dokter_id"
                                name="dokter_id"
                                x-model="selectedDokterId"
                                @change="updateDokterName($event)"
                                required
                                class="w-full px-4 py-2.5 text-sm border @error('dokter_id') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                            <option value="">-- Pilih Dokter & Spesialisasi --</option>
                            @foreach($dokters as $d)
                                <option value="{{ $d->id }}"
                                        data-nama="{{ $d->nama }}"
                                        data-spesialisasi="{{ $d->spesialisasi }}"
                                        {{ old('dokter_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }} — Spesialis {{ $d->spesialisasi }}
                                </option>
                            @endforeach
                        </select>
                        @error('dokter_id')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- CARD 2: JADWAL KEDATANGAN --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            2
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Jadwal & Waktu Kunjungan</h2>
                            <p class="text-xs text-gray-500">Tentukan tanggal dan estimasi jam kedatangan di klinik</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Tanggal Kunjungan --}}
                        <div>
                            <label for="tgl_kunjungan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Tanggal Kunjungan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date"
                                   id="tgl_kunjungan"
                                   name="tgl_kunjungan"
                                   x-model="tglKunjungan"
                                   required
                                   value="{{ old('tgl_kunjungan', date('Y-m-d')) }}"
                                   class="w-full px-4 py-2.5 text-sm border @error('tgl_kunjungan') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('tgl_kunjungan')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jam Kunjungan --}}
                        <div>
                            <label for="jam_kunjungan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Jam Kunjungan <span class="text-rose-500">*</span>
                            </label>
                            <input type="time"
                                   id="jam_kunjungan"
                                   name="jam_kunjungan"
                                   x-model="jamKunjungan"
                                   required
                                   value="{{ old('jam_kunjungan', date('H:i')) }}"
                                   class="w-full px-4 py-2.5 text-sm border @error('jam_kunjungan') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            @error('jam_kunjungan')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- CARD 3: KELUHAN PASIEN --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                            3
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Keluhan & Keterangan Klinis</h2>
                            <p class="text-xs text-gray-500">Catat gejala awal atau alasan pemeriksaan pasien</p>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="keluhan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Keluhan Utama <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-xs text-gray-400" x-text="keluhan.length + ' / 1000 karakter'"></span>
                        </div>
                        <textarea id="keluhan"
                                  name="keluhan"
                                  rows="4"
                                  x-model="keluhan"
                                  maxlength="1000"
                                  required
                                  placeholder="Contoh: Mengalami demam tinggi sejak 2 hari yang lalu, disertai batuk berdahak dan pusing kepala..."
                                  class="w-full px-4 py-2.5 text-sm border @error('keluhan') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('keluhan') }}</textarea>
                        @error('keluhan')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- FORM ACTIONS --}}
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('pendaftaran.index') }}"
                       class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-500/20 hover:shadow-md hover:shadow-blue-500/30 transition-all duration-150">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Daftarkan Sekarang</span>
                    </button>
                </div>
            </div>

            {{-- Kolom Kanan: Live Preview & Ringkasan Antrean (1 span) --}}
            <div class="space-y-6">

                {{-- KARTU TIKET PREVIEW --}}
                <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-800 text-white rounded-2xl p-6 shadow-lg shadow-blue-500/20 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>

                    <div class="flex items-center justify-between pb-4 border-b border-white/20">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-blue-200">Klinik Sehat</p>
                            <h3 class="text-lg font-bold">Tiket Antrean</h3>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm">
                            ⏳ Menunggu
                        </span>
                    </div>

                    <div class="py-4 space-y-3">
                        <div>
                            <p class="text-[11px] text-blue-200 uppercase font-semibold">Kode Pendaftaran</p>
                            <p class="font-mono text-xl font-bold tracking-wider mt-0.5">REG-{{ date('Ymd') }}XXXX</p>
                            <p class="text-[10px] text-blue-200 italic">*Nomor urut otomatis saat disimpan</p>
                        </div>

                        <div class="pt-2 border-t border-white/10">
                            <p class="text-[11px] text-blue-200 uppercase font-semibold">Nama Pasien</p>
                            <p class="text-sm font-bold truncate mt-0.5" x-text="pasienName || 'Belum memilih pasien'"></p>
                        </div>

                        <div class="pt-2 border-t border-white/10">
                            <p class="text-[11px] text-blue-200 uppercase font-semibold">Dokter Pemeriksa</p>
                            <p class="text-sm font-bold truncate mt-0.5" x-text="dokterName || 'Belum memilih dokter'"></p>
                            <p class="text-xs text-blue-200" x-text="dokterSpesialisasi ? 'Spesialis ' + dokterSpesialisasi : ''"></p>
                        </div>

                        <div class="pt-2 border-t border-white/10 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <p class="text-[11px] text-blue-200 uppercase font-semibold">Tanggal</p>
                                <p class="font-bold mt-0.5" x-text="tglKunjungan ? tglKunjungan : '-'"></p>
                            </div>
                            <div>
                                <p class="text-[11px] text-blue-200 uppercase font-semibold">Pukul</p>
                                <p class="font-bold mt-0.5" x-text="(jamKunjungan || '-') + ' WIB'"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KARTU INFORMASI PENTING --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm space-y-3 text-xs text-gray-600">
                    <div class="flex items-center gap-2 font-bold text-gray-900 text-sm">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Ketentuan Antrean</span>
                    </div>
                    <ul class="space-y-2 list-disc list-inside text-gray-500">
                        <li>Pasien diharapkan hadir 15 menit sebelum estimasi jam konsultasi.</li>
                        <li>Kode pendaftaran otomatis digenerate oleh sistem dengan format <code>REG-YYYYMMDDxxxx</code>.</li>
                        <li>Status antrean akan otomatis terdaftar sebagai <strong>Menunggu</strong> sampai dokter memulai pemeriksaan.</li>
                    </ul>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
function createPendaftaranForm() {
    return {
        selectedPasienId: '{{ old('pasien_id') }}',
        selectedDokterId: '{{ old('dokter_id') }}',
        pasienName: '',
        dokterName: '',
        dokterSpesialisasi: '',
        tglKunjungan: '{{ old('tgl_kunjungan', date('Y-m-d')) }}',
        jamKunjungan: '{{ old('jam_kunjungan', date('H:i')) }}',
        keluhan: '{{ old('keluhan') }}',

        init() {
            this.$nextTick(() => {
                const pEl = document.getElementById('pasien_id');
                if (pEl && pEl.selectedIndex > 0) {
                    this.pasienName = pEl.options[pEl.selectedIndex].getAttribute('data-nama') || '';
                }
                const dEl = document.getElementById('dokter_id');
                if (dEl && dEl.selectedIndex > 0) {
                    this.dokterName = dEl.options[dEl.selectedIndex].getAttribute('data-nama') || '';
                    this.dokterSpesialisasi = dEl.options[dEl.selectedIndex].getAttribute('data-spesialisasi') || '';
                }
            });
        },

        updatePasienName(event) {
            const opt = event.target.options[event.target.selectedIndex];
            this.pasienName = opt ? (opt.getAttribute('data-nama') || '') : '';
        },

        updateDokterName(event) {
            const opt = event.target.options[event.target.selectedIndex];
            this.dokterName = opt ? (opt.getAttribute('data-nama') || '') : '';
            this.dokterSpesialisasi = opt ? (opt.getAttribute('data-spesialisasi') || '') : '';
        }
    };
}
</script>
@endsection
