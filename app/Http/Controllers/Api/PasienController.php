<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePasienRequest;
use App\Http\Requests\UpdatePasienRequest;
use App\Http\Resources\PasienResource;
use App\Models\Pasien;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Pasien::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $pasien = $query->latest()->paginate($request->get('per_page', 10));

        return $this->paginated($pasien, PasienResource::class, 'Data pasien berhasil diambil');
    }

    public function store(StorePasienRequest $request)
    {
        $pasien = Pasien::create($request->validated());
        return $this->success(new PasienResource($pasien), 'Pasien berhasil ditambahkan', 201);
    }

    public function show(Pasien $pasien)
    {
        return $this->success(new PasienResource($pasien));
    }

    public function update(UpdatePasienRequest $request, Pasien $pasien)
    {
        $pasien->update($request->validated());
        return $this->success(new PasienResource($pasien), 'Pasien berhasil diupdate');
    }

    public function destroy(Pasien $pasien)
    {
        $pasien->delete();
        return $this->success(null, 'Pasien berhasil dihapus');
    }
}
