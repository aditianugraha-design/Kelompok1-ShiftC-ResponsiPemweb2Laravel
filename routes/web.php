<?php

use App\Http\Controllers\DokterController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekamMedisController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $stats = [
                'total_pasien' => \App\Models\Pasien::count(),
                'total_dokter' => \App\Models\Dokter::count(),
                'antrean_aktif' => \App\Models\Pendaftaran::whereIn('status', ['menunggu', 'diproses'])->count(),
                'pendaftaran_hari_ini' => \App\Models\Pendaftaran::whereDate('tgl_kunjungan', today())->count(),
                'selesai_hari_ini' => \App\Models\Pendaftaran::whereDate('tgl_kunjungan', today())->where('status', 'selesai')->count(),
                'total_rekam_medis' => \App\Models\RekamMedis::count(),
            ];
        } elseif ($user->isDokter()) {
            $dokter = \App\Models\Dokter::where('user_id', $user->id)->first();
            $stats = [
                'antrean_hari_ini' => \App\Models\Pendaftaran::where('dokter_id', $dokter?->id)->whereDate('tgl_kunjungan', today())->count(),
                'menunggu' => \App\Models\Pendaftaran::where('dokter_id', $dokter?->id)->where('status', 'menunggu')->count(),
                'diproses' => \App\Models\Pendaftaran::where('dokter_id', $dokter?->id)->where('status', 'diproses')->count(),
                'selesai_hari_ini' => \App\Models\Pendaftaran::where('dokter_id', $dokter?->id)->whereDate('tgl_kunjungan', today())->where('status', 'selesai')->count(),
                'total_pasien_ditangani' => \App\Models\Pendaftaran::where('dokter_id', $dokter?->id)->distinct('pasien_id')->count(),
                'total_rekam_medis' => \App\Models\RekamMedis::whereHas('pendaftaran', fn($q) => $q->where('dokter_id', $dokter?->id))->count(),
            ];
        } else {
            $pasien = \App\Models\Pasien::where('user_id', $user->id)->first();
            $stats = [
                'total_kunjungan' => \App\Models\Pendaftaran::where('pasien_id', $pasien?->id)->count(),
                'menunggu' => \App\Models\Pendaftaran::where('pasien_id', $pasien?->id)->where('status', 'menunggu')->count(),
                'diproses' => \App\Models\Pendaftaran::where('pasien_id', $pasien?->id)->where('status', 'diproses')->count(),
                'selesai' => \App\Models\Pendaftaran::where('pasien_id', $pasien?->id)->where('status', 'selesai')->count(),
                'total_rekam_medis' => \App\Models\RekamMedis::whereHas('pendaftaran', fn($q) => $q->where('pasien_id', $pasien?->id))->count(),
                'kunjungan_terakhir' => \App\Models\Pendaftaran::where('pasien_id', $pasien?->id)->latest()->first(),
            ];
        }

        $recentPendaftaran = \App\Models\Pendaftaran::with(['pasien', 'dokter'])
            ->when($user->isDokter(), function ($q) use ($user) {
                $d = \App\Models\Dokter::where('user_id', $user->id)->first();
                $q->where('dokter_id', $d?->id);
            })
            ->when($user->isPasien(), function ($q) use ($user) {
                $p = \App\Models\Pasien::where('user_id', $user->id)->first();
                $q->where('pasien_id', $p?->id);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('user', 'stats', 'recentPendaftaran'));
    })->name('dashboard');

    // ==========================================
    // MODUL DOKTER
    // ==========================================
    Route::get('/dokter', [DokterController::class, 'index'])->name('dokter.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('/dokter/create', [DokterController::class, 'create'])->name('dokter.create');
        Route::post('/dokter', [DokterController::class, 'store'])->name('dokter.store');
        Route::get('/dokter/{dokter}/edit', [DokterController::class, 'edit'])->name('dokter.edit');
        Route::put('/dokter/{dokter}', [DokterController::class, 'update'])->name('dokter.update');
        Route::delete('/dokter/{dokter}', [DokterController::class, 'destroy'])->name('dokter.destroy');
    });

    // ==========================================
    // MODUL PASIEN
    // ==========================================
    Route::get('/pasien', [PasienController::class, 'index'])->name('pasien.index');
    Route::get('/pasien/profil', [PasienController::class, 'profile'])->name('pasien.profile');
    Route::get('/pasien/lengkapi-profil', [PasienController::class, 'completeProfile'])->name('pasien.complete-profile');
    Route::post('/pasien/lengkapi-profil', [PasienController::class, 'storeCompleteProfile'])->name('pasien.store-complete-profile');

    Route::middleware('role:admin')->group(function () {
        Route::get('/pasien/create', [PasienController::class, 'create'])->name('pasien.create');
        Route::post('/pasien', [PasienController::class, 'store'])->name('pasien.store');
        Route::delete('/pasien/{pasien}', [PasienController::class, 'destroy'])->name('pasien.destroy');
    });

    Route::get('/pasien/{pasien}', [PasienController::class, 'show'])->name('pasien.show');
    Route::get('/pasien/{pasien}/edit', [PasienController::class, 'edit'])->name('pasien.edit');
    Route::put('/pasien/{pasien}', [PasienController::class, 'update'])->name('pasien.update');

    // ==========================================
    // MODUL PENDAFTARAN
    // ==========================================
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
    Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');

    Route::middleware('role:admin|dokter')->group(function () {
        Route::put('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');
    });

    // Rekam Medis (pakai Resource Controller)
    Route::resource('rekam-medis', RekamMedisController::class);

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

