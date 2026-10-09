@extends('layouts.app')

@section('title', 'Detail Pasien')

@section('header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            @if(auth()->user()->role === 'admin' || auth()->user()->hasRole('admin'))
                <a href="{{ route('pasien.index') }}" class="hover:text-blue-600 transition-colors">Pasien</a>
            @else
                <a href="{{ route('pasien.profile') }}" class="hover:text-blue-600 transition-colors">Profil Pasien</a>
            @endif
            <span>/</span>
            <span class="text-gray-800 font-medium">Detail</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Detail Pasien</h1>
        <p class="text-sm text-gray-500 mt-1">Informasi lengkap identitas dan riwayat kunjungan pasien</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('pasien.edit', $pasien) }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-150">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span>Ubah Data</span>
        </a>
        <a href="{{ auth()->user()->role === 'admin' || auth()->user()->hasRole('admin') ? route('pasien.index') : route('pasien.profile') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KIRI: PROFIL & IDENTITAS --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="h-20 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500"></div>
                <div class="px-6 pb-6 -mt-10 text-center">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-white border-4 border-white shadow-md flex items-center justify-center text-xl font-bold {{ $pasien->jenis_kelamin == 'L' ? 'text-sky-700 bg-sky-100' : 'text-rose-700 bg-rose-100' }}">
                        {{ strtoupper(substr($pasien->nama, 0, 2)) }}
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 mt-3">{{ $pasien->nama }}</h2>
                    <div class="flex items-center justify-center gap-2 mt-1.5 flex-wrap">
                        <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full">NIK: {{ $pasien->nik }}</span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $pasien->jenis_kelamin == 'L' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $pasien->jenis_kelamin == 'L' ? '♂ Laki-laki' : '♀ Perempuan' }}
                        </span>
                    </div>
                    @if($pasien->user)
                    <div class="mt-4 pt-4 border-t border-gray-100 text-left">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Akun Terhubung</p>
                        <p class="text-sm font-medium text-gray-800 mt-1">{{ $pasien->user->name }}</p>
                        <p class="text-xs text-gray-500 break-all">{{ $pasien->user->email }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-gray-900 pb-3 border-b border-gray-100 flex items-center gap-2">
                    <span>📞</span> Kontak & Domisili
                </h3>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-xs font-semibold text-gray-400 uppercase">No. Telepon</span>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">{{ $pasien->no_telp }}</span>
                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition-colors">
                            Hubungi WA
                        </a>
                    </div>
                </div>
                <div class="text-sm">
                    <span class="text-xs font-semibold text-gray-400 uppercase block">Alamat Domisili</span>
                    <p class="text-gray-700 mt-1 leading-relaxed">{{ $pasien->alamat }}</p>
                </div>
                <div class="pt-3 border-t border-gray-100 text-xs text-gray-400 flex justify-between">
                    <span>Terdaftar: <span class="text-gray-600">{{ $pasien->created_at ? $pasien->created_at->format('d/m/Y') : '-' }}</span></span>
                    <span>Diperbarui: <span class="text-gray-600">{{ $pasien->updated_at ? $pasien->updated_at->format('d/m/Y') : '-' }}</span></span>
                </div>
            </div>
        </div>

        {{-- KANAN: IDENTITAS & RIWAYAT --}}
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
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Jumlah Kunjungan</p>
                        <p class="font-bold text-blue-600 mt-1">{{ $pendaftarans->count() }} kali</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2"><span>📅</span> Riwayat Kunjungan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Menampilkan {{ $pendaftarans->count() }} data pendaftaran</p>
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
                                    <p class="text-xs text-gray-500 mt-1">Pasien belum pernah mendaftarkan kunjungan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
