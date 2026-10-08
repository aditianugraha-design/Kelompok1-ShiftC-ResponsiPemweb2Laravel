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
            'nip' => 'nullable|string|unique:dokters,nip|max:50',
            'spesialisasi' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'jadwal_praktik' => 'nullable|string|max:255',
            'status' => 'nullable|in:aktif,non-aktif',
        ];
    }
}