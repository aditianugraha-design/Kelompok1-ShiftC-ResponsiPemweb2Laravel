@extends('layouts.app')

@section('title', 'Detail Rekam Medis')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('rekam-medis.index') }}" class="hover:text-blue-600 transition-colors">Rekam Medis</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Detail</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Detail Rekam Medis</h1>
        <p class="text-sm text-gray-500 mt-1">Pendaftaran {{ $rekamMedis->pendaftaran?->kode_daftar ?? '-' }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('rekam-medis.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 shadow-sm transition-colors">
            <span>Kembali</span>
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isDokter())
        <a href="{{ route('rekam-medis.edit', $rekamMedis) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
            <span>Ubah</span>
        </a>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
        <div class="pb-4 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Pendaftaran Terkait</p>
            <p class="font-mono text-sm font-bold text-blue-600 mt-1">{{ $rekamMedis->pendaftaran?->kode_daftar ?? '-' }}</p>
            <p class="text-sm text-gray-700 mt-1">
                {{ $rekamMedis->pendaftaran?->pasien?->nama ?? '-' }}
                <span class="text-gray-400">/</span>
                {{ $rekamMedis->pendaftaran?->dokter?->nama ?? '-' }}
                <span class="text-gray-400">({{ $rekamMedis->pendaftaran?->dokter?->spesialisasi ?? '-' }})</span>
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Status pendaftaran: <span class="font-semibold capitalize">{{ $rekamMedis->pendaftaran?->status ?? '-' }}</span>
            </p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase">Diagnosa</p>
            <p class="text-sm text-gray-800 mt-1 leading-relaxed">{{ $rekamMedis->diagnosa }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase">Tindakan</p>
            <p class="text-sm text-gray-800 mt-1 leading-relaxed">{{ $rekamMedis->tindakan ?: '-' }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase">Resep</p>
            <p class="text-sm text-gray-800 mt-1 leading-relaxed">{{ $rekamMedis->resep ?: '-' }}</p>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase">Catatan</p>
            <p class="text-sm text-gray-800 mt-1 leading-relaxed">{{ $rekamMedis->catatan ?: '-' }}</p>
        </div>
    </div>

</div>
@endsection
