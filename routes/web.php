<?php

use App\Http\Controllers\DokterController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekamMedisController;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
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
                'total_pasien' => Pasien::count(),
                'total_dokter' => Dokter::count(),
                'antrean_aktif' => Pendaftaran::whereIn('status', ['menunggu', 'diproses'])->count(),
                'pendaftaran_hari_ini' => Pendaftaran::whereDate('tgl_kunjungan', today())->count(),
                'selesai_hari_ini' => Pendaftaran::whereDate('tgl_kunjungan', today())->where('status', 'selesai')->count(),
                'total_rekam_medis' => RekamMedis::count(),
            ];
        } elseif ($user->isDokter()) {
            $dokter = Dokter::where('user_id', $user->id)->first();
            // Antrean = hari ini, sama seperti halaman pendaftaran
            $stats = [
                'antrean_aktif' => Pendaftaran::where('dokter_id', $dokter?->id)->whereDate('tgl_kunjungan', today())->whereIn('status', ['menunggu', 'diproses'])->count(),
                'menunggu' => Pendaftaran::where('dokter_id', $dokter?->id)->whereDate('tgl_kunjungan', today())->where('status', 'menunggu')->count(),
                'diproses' => Pendaftaran::where('dokter_id', $dokter?->id)->whereDate('tgl_kunjungan', today())->where('status', 'diproses')->count(),
                'selesai_total' => Pendaftaran::where('dokter_id', $dokter?->id)->whereDate('tgl_kunjungan', today())->where('status', 'selesai')->count(),
                'total_pasien_ditangani' => Pendaftaran::where('dokter_id', $dokter?->id)->distinct('pasien_id')->count(),
                'total_rekam_medis' => RekamMedis::whereHas('pendaftaran', fn ($q) => $q->where('dokter_id', $dokter?->id))->count(),
            ];
        } else {
            $pasien = Pasien::where('user_id', $user->id)->first();
            $stats = [
                'total_kunjungan' => Pendaftaran::where('pasien_id', $pasien?->id)->count(),
                'menunggu' => Pendaftaran::where('pasien_id', $pasien?->id)->where('status', 'menunggu')->count(),
                'diproses' => Pendaftaran::where('pasien_id', $pasien?->id)->where('status', 'diproses')->count(),
                'selesai' => Pendaftaran::where('pasien_id', $pasien?->id)->where('status', 'selesai')->count(),
                'total_rekam_medis' => RekamMedis::whereHas('pendaftaran', fn ($q) => $q->where('pasien_id', $pasien?->id))->count(),
                'kunjungan_terakhir' => Pendaftaran::where('pasien_id', $pasien?->id)->latest()->first(),
            ];
        }

        $recentQuery = Pendaftaran::with(['pasien', 'dokter'])
            ->when($user->isDokter(), function ($q) use ($user) {
                $d = Dokter::where('user_id', $user->id)->first();
                $q->where('dokter_id', $d?->id)->whereDate('tgl_kunjungan', today());
            })
            ->when($user->isPasien(), function ($q) use ($user) {
                $p = Pasien::where('user_id', $user->id)->first();
                $q->where('pasien_id', $p?->id);
            });

        // Dokter: antrean hari ini urut jam paling awal; lainnya: terbaru
        $recentPendaftaran = $user->isDokter()
            ? $recentQuery->orderBy('jam_kunjungan', 'asc')->take(5)->get()
            : $recentQuery->latest()->take(5)->get();

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
    Route::patch('/pendaftaran/{pendaftaran}/batal', [PendaftaranController::class, 'cancel'])->name('pendaftaran.cancel');

    Route::middleware('role:admin|dokter')->group(function () {
        Route::put('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
        Route::patch('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.patch');
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'destroy'])->name('pendaftaran.destroy');
    });

    // Rekam Medis (pakai Resource Controller)
    Route::resource('rekam-medis', RekamMedisController::class)->parameters(['rekam-medis' => 'rekamMedis']);

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';