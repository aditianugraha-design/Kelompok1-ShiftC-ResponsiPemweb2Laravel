<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDokterRequest;
use App\Http\Requests\UpdateDokterRequest;
use App\Http\Resources\DokterResource;
use App\Models\Dokter;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokterController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Dokter::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('spesialisasi')) {
            $query->where('spesialisasi', $request->spesialisasi);
        }

        $dokter = $query->latest()->paginate($request->get('per_page', 10));

        return $this->paginated($dokter, DokterResource::class, 'Data dokter berhasil diambil');
    }

    public function store(StoreDokterRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('dokter', 'public');
        }

        $dokter = Dokter::create($data);
        return $this->success(new DokterResource($dokter), 'Dokter berhasil ditambahkan', 201);
    }

    public function show(Dokter $dokter)
    {
        return $this->success(new DokterResource($dokter));
    }

    public function update(UpdateDokterRequest $request, Dokter $dokter)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($dokter->foto && Storage::disk('public')->exists($dokter->foto)) {
                Storage::disk('public')->delete($dokter->foto);
            }
            $data['foto'] = $request->file('foto')->store('dokter', 'public');
        }

        $dokter->update($data);
        return $this->success(new DokterResource($dokter), 'Dokter berhasil diupdate');
    }

    public function destroy(Dokter $dokter)
    {
        if ($dokter->foto && Storage::disk('public')->exists($dokter->foto)) {
            Storage::disk('public')->delete($dokter->foto);
        }
        $dokter->delete();
        return $this->success(null, 'Dokter berhasil dihapus');
    }
}
