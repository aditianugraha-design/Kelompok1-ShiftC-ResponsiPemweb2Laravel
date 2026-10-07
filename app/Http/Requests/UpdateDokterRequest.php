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
            'no_sip' => ['required', 'string', Rule::unique('dokters', 'no_sip')->ignore($dokterId)],
            'spesialisasi' => 'required|string|max:100',
            'no_telp' => 'required|string|max:15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jadwal_praktik' => 'nullable|array',
        ];
    }
}
