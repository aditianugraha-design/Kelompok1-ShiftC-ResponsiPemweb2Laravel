<?php

use App\Http\Controllers\DokterController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ==========================================
    // MODUL DOKTER
    // ==========================================
    // Semua pengguna terautentikasi (Admin, Dokter, Pasien) dapat melihat katalog dokter & jadwal
    Route::get('/dokter', [DokterController::class, 'index'])->name('dokter.index');

    // HANYA ADMIN yang dapat menambah, mengubah, dan menghapus dokter
    Route::middleware('role:admin')->group(function () {
        Route::get('/dokter/create', [DokterController::class, 'create'])->name('dokter.create');
        Route::post('/dokter', [DokterController::class, 'store'])->name('dokter.store');
        Route::get('/dokter/{dokter}/edit', [DokterController::class, 'edit'])->name('dokter.edit');
        Route::put('/dokter/{dokter}', [DokterController::class, 'update'])->name('dokter.update');
        Route::delete('/dokter/{dokter}', [DokterController::class, 'destroy'])->name('dokter.destroy');
    });

    // ==========================================
    // MODUL PASIEN (Disiapkan untuk Anggota 2)
    // ==========================================
    Route::resource('pasien', PasienController::class)->only(['index', 'create', 'store', 'update', 'destroy']);

    // ==========================================
    // MODUL PENDAFTARAN (Disiapkan untuk Anggota 3)
    // ==========================================
    Route::resource('pendaftaran', PendaftaranController::class)->only(['index', 'create', 'store', 'update', 'destroy']);

    // ==========================================
    // MODUL REKAM MEDIS (Disiapkan untuk Anggota 4)
    // ==========================================
    Route::get('/rekam-medis', function () {
        return view('rekam-medis.index');
    })->name('rekam-medis.index');

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
