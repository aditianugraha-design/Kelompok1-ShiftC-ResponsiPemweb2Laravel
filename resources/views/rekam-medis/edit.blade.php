@extends('layouts.app')

@section('title', 'Ubah Rekam Medis')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('rekam-medis.index') }}" class="hover:text-blue-600 transition-colors">Rekam Medis</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Ubah</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Ubah Rekam Medis</h1>
        <p class="text-sm text-gray-500 mt-1">Pendaftaran {{ $rekamMedis->pendaftaran?->kode_daftar ?? '-' }}</p>
    </div>
    <div>
        <a href="{{ route('rekam-medis.show', $rekamMedis) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 shadow-sm transition-colors">
            <span>Kembali</span>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    @if(isset($errors) && $errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl shadow-sm">
        <h3 class="text-sm font-semibold">Terdapat kesalahan pada input formulir:</h3>
        <ul class="mt-1 list-disc list-inside text-sm space-y-0.5 text-rose-700">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('rekam-medis.update', $rekamMedis) }}" method="POST" class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
        @csrf
        @method('PUT')

        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
            <p class="text-xs font-semibold text-gray-500 uppercase">Pendaftaran Terkait (tidak dapat diubah)</p>
            <p class="font-mono text-sm font-bold text-blue-600 mt-1">{{ $rekamMedis->pendaftaran?->kode_daftar ?? '-' }}</p>
            <p class="text-sm text-gray-700 mt-0.5">{{ $rekamMedis->pendaftaran?->pasien?->nama ?? '-' }} / {{ $rekamMedis->pendaftaran?->dokter?->nama ?? '-' }}</p>
        </div>

        <div>
            <label for="diagnosa" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                Diagnosa <span class="text-rose-500">*</span>
            </label>
            <textarea id="diagnosa" name="diagnosa" rows="3" required maxlength="2000"
                class="w-full px-4 py-2.5 text-sm border @error('diagnosa') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('diagnosa', $rekamMedis->diagnosa) }}</textarea>
            @error('diagnosa')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tindakan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Tindakan</label>
            <textarea id="tindakan" name="tindakan" rows="2" maxlength="2000"
                class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('tindakan', $rekamMedis->tindakan) }}</textarea>
        </div>

        <div>
            <label for="resep" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Resep</label>
            <textarea id="resep" name="resep" rows="2" maxlength="2000"
                class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('resep', $rekamMedis->resep) }}</textarea>
        </div>

        <div>
            <label for="catatan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Catatan</label>
            <textarea id="catatan" name="catatan" rows="2" maxlength="2000"
                class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('catatan', $rekamMedis->catatan) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('rekam-medis.show', $rekamMedis) }}"
               class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection
