<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRekamMedisRequest;
use App\Http\Requests\UpdateRekamMedisRequest;
use App\Http\Resources\RekamMedisResource;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\RekamMedis;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekamMedisController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = RekamMedis::with(['pendaftaran.pasien', 'pendaftaran.dokter']);

        if ($user->isDokter()) {
            $dokter = Dokter::where('user_id', $user->id)->first();
            $query->whereHas('pendaftaran', fn ($q) => $q->where('dokter_id', $dokter?->id));
        } elseif ($user->isPasien()) {
            $pasien = Pasien::where('user_id', $user->id)->first();
            $query->whereHas('pendaftaran', fn ($q) => $q->where('pasien_id', $pasien?->id));
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $rekamMedis = $query->latest()->paginate($request->get('per_page', 10));

        return $this->paginated($rekamMedis, RekamMedisResource::class, 'Data rekam medis berhasil diambil');
    }

    public function store(StoreRekamMedisRequest $request)
    {
        $validated = $request->validated();

        $rekamMedis = DB::transaction(function () use ($validated) {
            $rekamMedis = RekamMedis::create($validated);
            // Business process: update status pendaftaran jadi selesai
            $rekamMedis->pendaftaran->update(['status' => 'selesai']);

            return $rekamMedis;
        });

        return $this->success(
            new RekamMedisResource($rekamMedis->load(['pendaftaran.pasien', 'pendaftaran.dokter'])),
            'Rekam medis berhasil dibuat',
            201
        );
    }

    public function show(Request $request, RekamMedis $rekamMedis)
    {
        $user = $request->user();

        $rekamMedis->load(['pendaftaran.pasien', 'pendaftaran.dokter']);

        if ($user->isPasien() && $user->pasien && $rekamMedis->pendaftaran->pasien_id !== $user->pasien->id) {
            return $this->error('Anda tidak memiliki akses ke data rekam medis ini.', 403);
        }

        if ($user->isDokter() && $user->dokter && $rekamMedis->pendaftaran->dokter_id !== $user->dokter->id) {
            return $this->error('Anda tidak memiliki akses ke data rekam medis ini.', 403);
        }

        return $this->success(new RekamMedisResource($rekamMedis));
    }

    public function update(UpdateRekamMedisRequest $request, RekamMedis $rekamMedis)
    {
        $rekamMedis->update($request->validated());

        return $this->success(
            new RekamMedisResource($rekamMedis->load(['pendaftaran.pasien', 'pendaftaran.dokter'])),
            'Rekam medis berhasil diperbarui.'
        );
    }

    public function destroy(RekamMedis $rekamMedis)
    {
        DB::transaction(function () use ($rekamMedis) {
            $rekamMedis->pendaftaran->update(['status' => 'diproses']);
            $rekamMedis->delete();
        });

        return $this->success(null, 'Rekam medis berhasil dihapus. Status pendaftaran dikembalikan ke Diproses.');
    }
}
