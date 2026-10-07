@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Total Pasien</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">21</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Total Dokter</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">6</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Total Pendaftaran</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">30</p>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Total Rekam Medis</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">10</p>
    </div>
</div>

<div class="mt-6 bg-white rounded-xl p-6 shadow-sm">
    <h2 class="text-xl font-semibold text-gray-800">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
    <p class="text-gray-500 mt-2">Anda login sebagai <span class="font-semibold capitalize">{{ auth()->user()->role }}</span>.</p>
</div>

@endsection

