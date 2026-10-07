<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDokterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'no_sip' => 'required|string|unique:dokters,no_sip|max:50',
            'spesialisasi' => 'required|string|max:100',
            'no_telp' => 'required|string|max:15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jadwal_praktik' => 'nullable|array',
        ];
    }
}