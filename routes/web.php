<?php

use App\Http\Controllers\DokterController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('pasien', PasienController::class)->only(['index', 'create', 'store', 'update', 'destroy']);

    Route::resource('dokter', DokterController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('pendaftaran', PendaftaranController::class)->only(['index', 'create', 'store', 'update', 'destroy']);

    Route::get('/rekam-medis', function () {
        return view('rekam-medis.index');
    })->name('rekam-medis.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
