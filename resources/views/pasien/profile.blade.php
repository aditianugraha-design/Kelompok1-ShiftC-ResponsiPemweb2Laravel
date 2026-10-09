@extends('layouts.app')

@section('title', 'Profil Saya')

@section('header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Profil Pasien</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Profil Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Informasi identitas pasien dan riwayat kunjungan Anda di klinik</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('pendaftaran.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Daftar Kunjungan</span>
        </a>
        <a href="{{ route('pasien.edit', $pasien) }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm shadow-blue-500/20 transition-all duration-150">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span>Edit Profil</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- ALERT FLASH --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
         class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    @endif

    @php
        $statusStyles = [
            'menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
            'diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
            'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'batal' => 'bg-rose-50 text-rose-700 border-rose-200',
        ];
        $statusIcons = ['menunggu' => '⏳', 'diproses' => '⚡', 'selesai' => '✅', 'batal' => '❌'];
        $cleanPhone = preg_replace('/[^0-9]/', '', $pasien->no_telp);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
    @endphp

    {{-- HERO PROFIL --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="h-24 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500"></div>
        <div class="px-6 pb-6 -mt-12">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="w-24 h-24 rounded-2xl bg-white border-4 border-white shadow-md flex items-center justify-center text-2xl font-bold {{ $pasien->jenis_kelamin == 'L' ? 'text-sky-700 bg-sky-100' : 'text-rose-700 bg-rose-100' }}">
                    {{ strtoupper(substr($pasien->nama, 0, 2)) }}
                </div>
                <div class="flex-1 sm:pb-2">
                    <h2 class="text-xl font-bold text-gray-900">{{ $pasien->nama }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full">NIK: {{ $pasien->nik }}</span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $pasien->jenis_kelamin == 'L' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $pasien->jenis_kelamin == 'L' ? '♂ Laki-laki' : '♀ Perempuan' }}
                        </span>
                        @if($pasien->golongan_darah)
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                            Gol. Darah {{ $pasien->golongan_darah }}
                        </span>
                        @endif
                        <span class="text-xs text-gray-500 bg-gray-50 px-2.5 py-1 rounded-full border border-gray-200">
                            Terdaftar {{ $pasien->created_at ? $pasien->created_at->format('d/m/Y') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KIRI: DATA IDENTITAS --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 pb-4 mb-4 border-b border-gray-100 flex items-center gap-2">
                    <span>🪪</span> Data Identitas
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">NIK</p>
                        <p class="font-mono font-medium text-gray-800 mt-1">{{ $pasien->nik }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Lengkap</p>
                        <p class="font-medium text-gray-800 mt-1">{{ $pasien->nama }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Lahir</p>
                        <p class="font-medium text-gray-800 mt-1">
                            {{ $pasien->tgl_lahir ? $pasien->tgl_lahir->format('d F Y') : '-' }}
                            @if($pasien->tgl_lahir) <span class="text-gray-400 text-xs">({{ $pasien->tgl_lahir->age }} thn)</span> @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Jenis Kelamin</p>
                        <p class="font-medium text-gray-800 mt-1">{{ $pasien->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Golongan Darah</p>
                        <p class="font-bold text-gray-800 mt-1">{{ $pasien->golongan_darah ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">No. Telepon</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="font-medium text-gray-800">{{ $pasien->no_telp }}</span>
                            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Alamat Domisili</p>
                        <p class="text-gray-800 mt-1 leading-relaxed">{{ $pasien->alamat }}</p>
                    </div>
                </div>
            </div>

            {{-- RIWAYAT PENDAFTARAN --}}
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2"><span>📅</span> Riwayat Kunjungan</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Menampilkan {{ $pendaftarans->count() }} data pendaftaran</p>
                    </div>
                    <a href="{{ route('pendaftaran.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                        Lihat Semua
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50/75 text-xs uppercase text-gray-500 tracking-wider border-b border-gray-200/60 font-semibold">
                            <tr>
                                <th scope="col" class="py-3 px-4">Kode</th>
                                <th scope="col" class="py-3 px-4">Tanggal & Jam</th>
                                <th scope="col" class="py-3 px-4">Dokter</th>
                                <th scope="col" class="py-3 px-4">Keluhan</th>
                                <th scope="col" class="py-3 px-4 text-center">Status</th>
                                <th scope="col" class="py-3 px-4">Diagnosa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pendaftarans as $daftar)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-xs font-semibold text-gray-800">{{ $daftar->kode_daftar }}</td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-xs">
                                    <p class="font-medium text-gray-800">{{ $daftar->tgl_kunjungan ? $daftar->tgl_kunjungan->format('d/m/Y') : '-' }}</p>
                                    <p class="text-gray-400">{{ $daftar->jam_kunjungan }}</p>
                                </td>
                                <td class="py-3.5 px-4 text-xs">
                                    <p class="font-medium text-gray-800">{{ $daftar->dokter?->nama ?? '-' }}</p>
                                    <p class="text-gray-400">{{ $daftar->dokter?->spesialisasi }}</p>
                                </td>
                                <td class="py-3.5 px-4 text-xs max-w-[200px]">
                                    <p class="truncate" title="{{ $daftar->keluhan }}">{{ $daftar->keluhan }}</p>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusStyles[$daftar->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                        {{ $statusIcons[$daftar->status] ?? '' }} <span class="capitalize">{{ $daftar->status }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-gray-500">
                                    {{ $daftar->rekamMedis?->diagnosa ?? ($daftar->status == 'selesai' ? '-' : 'Belum tersedia') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center">
                                    <p class="text-sm font-semibold text-gray-700">Belum ada riwayat kunjungan</p>
                                    <p class="text-xs text-gray-500 mt-1 mb-3">Mulai dengan mendaftarkan kunjungan ke dokter</p>
                                    <a href="{{ route('pendaftaran.create') }}"
                                       class="inline-flex px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors">
                                        + Daftar Kunjungan
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- KANAN: AKUN & RINGKASAN --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 pb-4 mb-4 border-b border-gray-100 flex items-center gap-2">
                    <span>🔐</span> Informasi Akun
                </h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Akun</p>
                        <p class="font-medium text-gray-800 mt-1">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</p>
                        <p class="font-medium text-gray-800 mt-1 break-all">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Peran</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-bold bg-sky-100 text-sky-700 uppercase tracking-wide mt-1">
                            Pasien
                        </span>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}"
                   class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                    Ubah Password / Email
                </a>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 pb-4 mb-4 border-b border-gray-100 flex items-center gap-2">
                    <span>📈</span> Ringkasan
                </h3>
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="p-3 bg-blue-50 rounded-xl">
                        <p class="text-xl font-bold text-blue-600">{{ $pendaftarans->count() }}</p>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-0.5">Total Kunjungan</p>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-xl">
                        <p class="text-xl font-bold text-emerald-600">{{ $pendaftarans->where('status', 'selesai')->count() }}</p>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-0.5">Selesai</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-xl">
                        <p class="text-xl font-bold text-amber-600">{{ $pendaftarans->where('status', 'menunggu')->count() }}</p>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-0.5">Menunggu</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-xl">
                        <p class="text-xl font-bold text-indigo-600">{{ $pendaftarans->where('status', 'diproses')->count() }}</p>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-0.5">Diproses</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
