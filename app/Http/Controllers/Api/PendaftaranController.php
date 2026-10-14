<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePendaftaranRequest;
use App\Http\Requests\UpdatePendaftaranRequest;
use App\Http\Resources\PendaftaranResource;
use App\Models\Pendaftaran;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Pendaftaran::with(['pasien', 'dokter', 'rekamMedis']);

        // ===== OTORISASI BERBASIS ROLE =====
        if ($user->isPasien()) {
            // Pasien hanya bisa melihat pendaftaran miliknya sendiri
            if ($user->pasien) {
                $query->where('pasien_id', $user->pasien->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->isDokter()) {
            // Dokter hanya melihat antrean pasien yang mendaftar ke dirinya (semua tanggal).
            if ($user->dokter) {
                $query->where('dokter_id', $user->dokter->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        // Admin: tidak ada batasan tambahan

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_daftar', 'like', "%{$search}%")
                    ->orWhere('keluhan', 'like', "%{$search}%")
                    ->orWhereHas('pasien', fn ($qp) => $qp->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                    )
                    ->orWhereHas('dokter', fn ($qd) => $qd->where('nama', 'like', "%{$search}%")
                        ->orWhere('spesialisasi', 'like', "%{$search}%")
                    );
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Dokter (admin saja)
        if ($request->filled('dokter_id') && $user->isAdmin()) {
            $query->where('dokter_id', $request->dokter_id);
        }

        // Filter Pasien (admin saja)
        if ($request->filled('pasien_id') && $user->isAdmin()) {
            $query->where('pasien_id', $request->pasien_id);
        }

        // Filter Tanggal Kunjungan
        if ($request->filled('tgl_kunjungan')) {
            $query->whereDate('tgl_kunjungan', $request->tgl_kunjungan);
        }

        // Antrean = hari ini: default kunjungan hari ini untuk admin & dokter.
        // Pasien tetap melihat seluruh riwayatnya.
        if (! $user->isPasien() && ! $request->filled('tgl_kunjungan')) {
            $query->whereDate('tgl_kunjungan', today());
        }

        if ($user->isPasien()) {
            $pendaftaran = $query->latest()->paginate($request->get('per_page', 10));
        } else {
            // Urut antrean dari jam paling awal agar pasien terdepan diperiksa dulu
            $pendaftaran = $query->orderBy('jam_kunjungan', 'asc')->orderBy('id', 'asc')->paginate($request->get('per_page', 10));
        }

        return $this->paginated($pendaftaran, PendaftaranResource::class, 'Data pendaftaran berhasil diambil');
    }

    public function store(StorePendaftaranRequest $request)
    {
        $data = $request->validated();
        $data['kode_daftar'] = Pendaftaran::generateKodeDaftar();
        $data['status'] = 'menunggu';

        $pendaftaran = Pendaftaran::create($data);

        return $this->success(
            new PendaftaranResource($pendaftaran->load(['pasien', 'dokter'])),
            'Pendaftaran berhasil dibuat',
            201
        );
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $user = auth()->user();

        // Pasien hanya boleh melihat pendaftaran miliknya
        if ($user->isPasien() && $user->pasien && $pendaftaran->pasien_id !== $user->pasien->id) {
            return $this->error('Anda tidak memiliki akses ke data pendaftaran ini.', 403);
        }

        // Dokter hanya boleh melihat pendaftaran yang ke dirinya
        if ($user->isDokter() && $user->dokter && $pendaftaran->dokter_id !== $user->dokter->id) {
            return $this->error('Anda tidak memiliki akses ke data pendaftaran ini.', 403);
        }

        $pendaftaran->load(['pasien', 'dokter', 'rekamMedis']);

        return $this->success(new PendaftaranResource($pendaftaran));
    }

    public function update(UpdatePendaftaranRequest $request, Pendaftaran $pendaftaran)
    {
        $to = $request->input('status');

        if ($to === null || $to === '') {
            return $this->error('Status pendaftaran wajib diisi.', 422);
        }

        if (! $pendaftaran->canTransitionTo($to)) {
            return $this->error("Transisi status dari {$pendaftaran->status} ke {$to} tidak diizinkan.", 422);
        }

        $pendaftaran->update($request->validated());

        return $this->success(
            new PendaftaranResource($pendaftaran->load(['pasien', 'dokter', 'rekamMedis'])),
            'Pendaftaran berhasil diupdate'
        );
    }

    public function cancel(Request $request, Pendaftaran $pendaftaran)
    {
        $user = $request->user();

        if (! $user->isPasien() || ! $user->pasien || $pendaftaran->pasien_id !== $user->pasien->id) {
            return $this->error('Anda tidak memiliki akses ke data pendaftaran ini.', 403);
        }

        if ($pendaftaran->status !== Pendaftaran::STATUS_MENUNGGU) {
            return $this->error('Hanya pendaftaran menunggu yang dapat dibatalkan.', 422);
        }

        $pendaftaran->update(['status' => Pendaftaran::STATUS_BATAL]);

        return $this->success(
            new PendaftaranResource($pendaftaran->load(['pasien', 'dokter', 'rekamMedis'])),
            'Pendaftaran berhasil dibatalkan.'
        );
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        $user = auth()->user();

        // Hanya admin yang bisa menghapus
        if (! $user->isAdmin()) {
            return $this->error('Hanya admin yang dapat menghapus data pendaftaran.', 403);
        }

        if ($pendaftaran->status !== 'menunggu') {
            return $this->error('Hanya pendaftaran dengan status menunggu yang bisa dihapus.', 400);
        }

        $pendaftaran->delete();

        return $this->success(null, 'Pendaftaran berhasil dihapus');
    }
}