<?php

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

    // Pasien
    Route::get('/pasien', fn() => view('pasien.index'))->name('pasien.index');

    // Dokter
    Route::get('/dokter', fn() => view('dokter.index'))->name('dokter.index');

    // Pendaftaran
    Route::get('/pendaftaran', fn() => view('pendaftaran.index'))->name('pendaftaran.index');

    // Rekam Medis (pakai Resource Controller)
    Route::resource('rekam-medis', RekamMedisController::class);

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

