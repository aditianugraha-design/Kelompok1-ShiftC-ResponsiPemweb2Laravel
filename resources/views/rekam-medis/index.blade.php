@extends('layouts.app')

@section('title', 'Rekam Medis')

@section('header')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Rekam Medis</span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 tracking-tight">Rekam Medis Pasien</h1>
        <p class="text-sm text-gray-500 mt-1">Hasil diagnosa dan tindakan dokter per kunjungan</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isDokter())
    <div>
        <a href="{{ route('rekam-medis.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Rekam Medis</span>
        </a>
    </div>
    @endif
</div>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm">
        <form method="GET" action="{{ route('rekam-medis.index') }}" class="flex flex-col md:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari diagnosa atau tindakan..."
                class="flex-1 px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-xl transition-colors">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('rekam-medis.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors text-center">
                Reset
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <th class="py-3 px-4 font-semibold">Kode Daftar</th>
                        <th class="py-3 px-4 font-semibold">Pasien</th>
                        <th class="py-3 px-4 font-semibold">Dokter</th>
                        <th class="py-3 px-4 font-semibold">Diagnosa</th>
                        <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rekamMedis as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-3 px-4 font-mono text-xs font-bold text-blue-600 whitespace-nowrap">
                            {{ $item->pendaftaran?->kode_daftar ?? '-' }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">{{ $item->pendaftaran?->pasien?->nama ?? '-' }}</td>
                        <td class="py-3 px-4 whitespace-nowrap">{{ $item->pendaftaran?->dokter?->nama ?? '-' }}</td>
                        <td class="py-3 px-4 max-w-xs">
                            <p class="line-clamp-2 text-gray-700">{{ $item->diagnosa }}</p>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('rekam-medis.show', $item) }}"
                                   class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if(auth()->user()->isAdmin() || auth()->user()->isDokter())
                                <a href="{{ route('rekam-medis.edit', $item) }}"
                                   class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-colors" title="Ubah">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('rekam-medis.destroy', $item) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus rekam medis ini? Status pendaftaran kembali ke Diproses.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-4 text-center">
                            <h3 class="text-base font-semibold text-gray-900">Belum ada data rekam medis</h3>
                            <p class="text-xs text-gray-500 mt-1">Rekam medis muncul setelah dokter mengisi hasil pemeriksaan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rekamMedis->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $rekamMedis->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
