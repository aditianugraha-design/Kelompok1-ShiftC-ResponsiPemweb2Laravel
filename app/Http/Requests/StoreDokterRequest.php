<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDokterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->role === 'admin' || $this->user()->hasRole('admin'));
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|unique:dokters,nip|max:50',
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
            'nip.unique' => 'NIP/STR dokter sudah terdaftar di sistem.',
            'spesialisasi.required' => 'Spesialisasi dokter wajib dipilih/diisi.',
            'no_telepon.required' => 'Nomor telepon wajib diisi.',
            'status.in' => 'Status dokter harus berupa aktif atau non-aktif.',
        ];
    }
}