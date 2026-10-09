<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRekamMedisRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isAdmin() || $user->isDokter());
    }

    public function rules(): array
    {
        return [
            'diagnosa' => 'required|string|max:2000',
            'tindakan' => 'nullable|string|max:2000',
            'resep'    => 'nullable|string|max:2000',
            'catatan'  => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'diagnosa.required' => 'Diagnosa wajib diisi.',
            'diagnosa.max'      => 'Diagnosa tidak boleh lebih dari 2000 karakter.',
            'tindakan.max'      => 'Tindakan tidak boleh lebih dari 2000 karakter.',
            'resep.max'         => 'Resep tidak boleh lebih dari 2000 karakter.',
            'catatan.max'       => 'Catatan tidak boleh lebih dari 2000 karakter.',
        ];
    }
}

