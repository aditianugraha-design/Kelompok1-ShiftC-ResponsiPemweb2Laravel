<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDokterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $dokterId = $this->route('dokter')->id ?? $this->route('dokter');

        return [
            'nama' => 'required|string|max:255',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('dokters', 'nip')->ignore($dokterId)],
            'spesialisasi' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jadwal_praktik' => 'nullable|string|max:255',
            'status' => 'nullable|in:aktif,non-aktif',
        ];
    }
}
