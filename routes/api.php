<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DokterController;
use App\Http\Controllers\Api\PasienController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\RekamMedisController;
use Illuminate\Support\Facades\Route;

// ==================== PUBLIC ROUTES ====================
Route::name('api.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    // ==================== PROTECTED ROUTES ====================
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [AuthController::class, 'me'])->name('me');
        });

        // Semua role bisa lihat daftar dokter
        Route::get('/dokter', [DokterController::class, 'index'])->name('dokter.index');
        Route::get('/dokter/{dokter}', [DokterController::class, 'show'])->name('dokter.show');

        // Admin only
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('pasien', PasienController::class);
            Route::post('/dokter', [DokterController::class, 'store'])->name('dokter.store');
            Route::put('/dokter/{dokter}', [DokterController::class, 'update'])->name('dokter.update');
            Route::delete('/dokter/{dokter}', [DokterController::class, 'destroy'])->name('dokter.destroy');
        });

        // Semua role terautentikasi bisa akses pendaftaran & rekam medis
        Route::apiResource('pendaftaran', PendaftaranController::class);
        Route::apiResource('rekam-medis', RekamMedisController::class);
    });
});

