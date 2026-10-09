<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDokterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->role === 'admin' || $this->user()->hasRole('admin'));
    }

    public function rules(): array
    {
        $dokter = $this->route('dokter');
        $dokterId = is_object($dokter) ? $dokter->id : $dokter;

        return [
            'nama' => 'required|string|max:255',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('dokters', 'nip')->ignore($dokterId)],
            'spesialisasi' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'jadwal_praktik' => 'nullable|string|max:255',
            'status' => 'nullable|in:aktif,non-aktif',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama dokter wajib diisi.',
            'nip.unique' => 'NIP/STR dokter sudah digunakan oleh dokter lain.',
            'spesialisasi.required' => 'Spesialisasi dokter wajib dipilih/diisi.',
            'no_telepon.required' => 'Nomor telepon wajib diisi.',
            'status.in' => 'Status dokter harus berupa aktif atau non-aktif.',
        ];
    }
}
