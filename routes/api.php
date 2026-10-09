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

    // ==================== PROTECTED ROUTES (SANCTUM) ====================
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [AuthController::class, 'me'])->name('me');
        });

        // ==================== PASIEN ENDPOINTS (PROFIL SENDIRI) ====================
        // Didefinisikan sebelum apiResource agar /pasien/me tidak tertangkap /pasien/{pasien}
        Route::get('/pasien/me', [PasienController::class, 'myProfile'])->name('pasien.me');
        Route::put('/pasien/me', [PasienController::class, 'updateMyProfile'])->name('pasien.update-my-profile');

        // ==================== DOKTER ENDPOINTS ====================
        // Semua role terautentikasi dapat membaca data dokter
        Route::get('/dokter', [DokterController::class, 'index'])->name('dokter.index');
        Route::get('/dokter/{dokter}', [DokterController::class, 'show'])->name('dokter.show');

        // ==================== ADMIN ONLY ENDPOINTS ====================
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('pasien', PasienController::class);
            Route::post('/dokter', [DokterController::class, 'store'])->name('dokter.store');
            Route::put('/dokter/{dokter}', [DokterController::class, 'update'])->name('dokter.update');
            Route::delete('/dokter/{dokter}', [DokterController::class, 'destroy'])->name('dokter.destroy');
        });

        // ==================== PENDAFTARAN ENDPOINTS ====================
        Route::apiResource('pendaftaran', PendaftaranController::class);

        // ==================== REKAM MEDIS ENDPOINTS ====================
        // Baca data rekam medis
        Route::get('/rekam-medis', [RekamMedisController::class, 'index'])->name('rekam-medis.index');
        Route::get('/rekam-medis/{rekam_medi}', [RekamMedisController::class, 'show'])->name('rekam-medis.show');

        // Hanya Dokter & Admin yang dapat membuat, mengubah, atau menghapus rekam medis
        Route::middleware('role:admin|dokter')->group(function () {
            Route::post('/rekam-medis', [RekamMedisController::class, 'store'])->name('rekam-medis.store');
            Route::put('/rekam-medis/{rekam_medi}', [RekamMedisController::class, 'update'])->name('rekam-medis.update');
            Route::delete('/rekam-medis/{rekam_medi}', [RekamMedisController::class, 'destroy'])->name('rekam-medis.destroy');
        });
    });
});
