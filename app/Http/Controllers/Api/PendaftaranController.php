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
        $query = Pendaftaran::with(['pasien', 'dokter', 'rekamMedis']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dokter_id')) {
            $query->where('dokter_id', $request->dokter_id);
        }

        if ($request->filled('pasien_id')) {
            $query->where('pasien_id', $request->pasien_id);
        }

        $pendaftaran = $query->latest()->paginate($request->get('per_page', 10));

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
        $pendaftaran->load(['pasien', 'dokter', 'rekamMedis']);
        return $this->success(new PendaftaranResource($pendaftaran));
    }

    public function update(UpdatePendaftaranRequest $request, Pendaftaran $pendaftaran)
    {
        $pendaftaran->update($request->validated());
        return $this->success(
            new PendaftaranResource($pendaftaran->load(['pasien', 'dokter', 'rekamMedis'])),
            'Pendaftaran berhasil diupdate'
        );
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->status !== 'menunggu') {
            return $this->error('Hanya pendaftaran dengan status menunggu yang bisa dihapus', 400);
        }

        $pendaftaran->delete();
        return $this->success(null, 'Pendaftaran berhasil dihapus');
    }
}
