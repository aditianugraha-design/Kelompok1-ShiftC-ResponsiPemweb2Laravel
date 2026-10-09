<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePasienRequest;
use App\Http\Requests\UpdatePasienRequest;
use App\Http\Resources\PasienResource;
use App\Models\Pasien;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function myProfile(Request $request)
    {
        $pasien = $request->user()->pasien;

        if (! $pasien) {
            return $this->error('Profil pasien belum dilengkapi.', 404);
        }

        return $this->success(new PasienResource($pasien));
    }

    public function updateMyProfile(Request $request)
    {
        $pasien = $request->user()->pasien;

        if (! $pasien) {
            return $this->error('Profil pasien belum dilengkapi.', 404);
        }

        $data = $request->validate([
            'nik' => ['required', 'digits:16', Rule::unique('pasiens', 'nik')->ignore($pasien->id)],
            'nama' => 'required|string|max:255',
            'tgl_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_telp.required' => 'No. telepon wajib diisi.',
        ]);

        $pasien->update($data);

        return $this->success(new PasienResource($pasien), 'Profil pasien berhasil diperbarui');
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
