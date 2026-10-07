<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRekamMedisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasAnyRole(['admin', 'dokter']);
    }

    public function rules(): array
    {
        return [
            'pendaftaran_id' => 'required|exists:pendaftarans,id|unique:rekam_medis,pendaftaran_id',
            'diagnosa' => 'required|string',
            'tindakan' => 'nullable|string',
            'resep' => 'nullable|string',
            'catatan' => 'nullable|string',
        ];
    }
}
