<?php

namespace App\Http\Requests;

use App\Models\Pasien;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePasienRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole('admin') || $user->role === 'admin') {
            return true;
        }

        $pasien = $this->route('pasien');

        return $pasien instanceof Pasien
            && $user->pasien !== null
            && $user->pasien->id === $pasien->id;
    }

    public function rules(): array
    {
        $pasien = $this->route('pasien');
        $pasienId = $pasien instanceof Pasien ? $pasien->id : $pasien;

        return [
            'nik' => ['required', 'digits:16', Rule::unique('pasiens', 'nik')->ignore($pasienId)],
            'nama' => 'required|string|max:255',
            'tgl_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_telp.required' => 'No. telepon wajib diisi.',
        ];
    }
}
