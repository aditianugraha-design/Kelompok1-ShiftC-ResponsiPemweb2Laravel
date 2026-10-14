@extends('layouts.app')

@section('title', 'Tambah Rekam Medis')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('rekam-medis.index') }}" class="hover:text-blue-600 transition-colors">Rekam Medis</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Tambah Baru</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Form Rekam Medis</h1>
        <p class="text-sm text-gray-500 mt-1">Isi diagnosa dan tindakan dokter. Menyimpan rekam medis otomatis mengubah status pendaftaran menjadi Selesai.</p>
    </div>
    <div>
        <a href="{{ route('rekam-medis.index') }}"
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

    @if($pendaftarans->isEmpty())
    <div class="bg-white rounded-2xl p-8 border border-gray-200/80 shadow-sm text-center">
        <h3 class="text-base font-semibold text-gray-900">Tidak ada pendaftaran yang bisa dibuatkan rekam medis</h3>
        <p class="text-sm text-gray-500 mt-2">Hanya pendaftaran berstatus menunggu/diproses yang belum memiliki rekam medis yang tampil di sini.</p>
        <a href="{{ route('pendaftaran.index') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <span>Lihat Pendaftaran</span>
        </a>
    </div>
    @else
    <form action="{{ route('rekam-medis.store') }}" method="POST" class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
        @csrf

        <div>
            <label for="pendaftaran_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                Pendaftaran <span class="text-rose-500">*</span>
            </label>
            <select id="pendaftaran_id"
                    name="pendaftaran_id"
                    required
                    class="w-full px-4 py-2.5 text-sm border @error('pendaftaran_id') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                <option value="">-- Pilih Pendaftaran (menunggu/diproses) --</option>
                @foreach($pendaftarans as $p)
                    <option value="{{ $p->id }}"
                        {{ (string) old('pendaftaran_id', request('pendaftaran_id', $selectedPendaftaran?->id)) === (string) $p->id ? 'selected' : '' }}>
                        {{ $p->kode_daftar }} — {{ $p->pasien?->nama ?? '-' }} / {{ $p->dokter?->nama ?? '-' }} ({{ ucfirst($p->status) }})
                    </option>
                @endforeach
            </select>
            @error('pendaftaran_id')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="diagnosa" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                Diagnosa <span class="text-rose-500">*</span>
            </label>
            <textarea id="diagnosa" name="diagnosa" rows="3" required maxlength="2000"
                placeholder="Contoh: ISPA ringan, observasi 3 hari..."
                class="w-full px-4 py-2.5 text-sm border @error('diagnosa') border-rose-400 bg-rose-50/20 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('diagnosa') }}</textarea>
            @error('diagnosa')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tindakan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Tindakan</label>
            <textarea id="tindakan" name="tindakan" rows="2" maxlength="2000"
                placeholder="Contoh: Nebulizer, istirahat cukup..."
                class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('tindakan') }}</textarea>
        </div>

        <div>
            <label for="resep" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Resep</label>
            <textarea id="resep" name="resep" rows="2" maxlength="2000"
                placeholder="Contoh: Paracetamol 3x1, Ambroxol 2x1..."
                class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('resep') }}</textarea>
        </div>

        <div>
            <label for="catatan" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Catatan</label>
            <textarea id="catatan" name="catatan" rows="2" maxlength="2000"
                placeholder="Catatan tambahan untuk pasien..."
                class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">{{ old('catatan') }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('rekam-medis.index') }}"
               class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
                <span>Simpan Rekam Medis</span>
            </button>
        </div>
    </form>
    @endif

</div>
@endsection
