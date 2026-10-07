<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRekamMedisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasAnyRole(['admin', 'dokter']);
    }

    public function rules(): array
    {
        return [
            'diagnosa' => 'sometimes|string',
            'tindakan' => 'nullable|string',
            'resep' => 'nullable|string',
            'catatan' => 'nullable|string',
        ];
    }
}
