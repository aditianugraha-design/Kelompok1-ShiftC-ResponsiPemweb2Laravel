<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRekamMedisRequest;
use App\Http\Requests\UpdateRekamMedisRequest;
use App\Http\Resources\RekamMedisResource;
use App\Models\RekamMedis;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class RekamMedisController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = RekamMedis::with(['pendaftaran.pasien', 'pendaftaran.dokter']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $rekamMedis = $query->latest()->paginate($request->get('per_page', 10));

        return $this->paginated($rekamMedis, RekamMedisResource::class, 'Data rekam medis berhasil diambil');
    }

    public function store(StoreRekamMedisRequest $request)
    {
        $rekamMedis = RekamMedis::create($request->validated());

        // Update status pendaftaran menjadi selesai (BUSINESS PROCESS)
        $rekamMedis->pendaftaran->update(['status' => 'selesai']);

        return $this->success(
            new RekamMedisResource($rekamMedis->load('pendaftaran')),
            'Rekam medis berhasil dibuat, status pendaftaran menjadi selesai',
            201
        );
    }

    public function show(RekamMedis $rekamMedis)
    {
        $rekamMedis->load('pendaftaran.pasien', 'pendaftaran.dokter');
        return $this->success(new RekamMedisResource($rekamMedis));
    }

    public function update(UpdateRekamMedisRequest $request, RekamMedis $rekamMedis)
    {
        $rekamMedis->update($request->validated());
        return $this->success(new RekamMedisResource($rekamMedis), 'Rekam medis berhasil diupdate');
    }

    public function destroy(RekamMedis $rekamMedis)
    {
        $rekamMedis->delete();
        return $this->success(null, 'Rekam medis berhasil dihapus');
    }
}
