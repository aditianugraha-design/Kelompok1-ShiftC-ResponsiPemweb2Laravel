<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasAnyRole(['admin', 'dokter']);
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:menunggu,diproses,selesai,batal',
            'keluhan' => 'sometimes|string|max:1000',
        ];
    }
}
