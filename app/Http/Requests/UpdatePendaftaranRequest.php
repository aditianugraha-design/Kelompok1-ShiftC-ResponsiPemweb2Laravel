<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        // Hanya admin dan dokter yang bisa memperbarui status pendaftaran
        return $user && ($user->isAdmin() || $user->isDokter());
    }

    public function rules(): array
    {
        return [
            'status'        => ['required', Rule::in(['menunggu', 'diproses', 'selesai', 'batal'])],
            'dokter_id'     => 'sometimes|nullable|exists:dokters,id',
            'tgl_kunjungan' => 'sometimes|nullable|date',
            'jam_kunjungan' => 'sometimes|nullable|date_format:H:i',
            'keluhan'       => 'sometimes|nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status pendaftaran wajib diisi.',
            'status.in'       => 'Status tidak valid. Pilih salah satu: menunggu, diproses, selesai, atau batal.',
            'dokter_id.exists'          => 'Dokter yang dipilih tidak ditemukan.',
            'tgl_kunjungan.date'        => 'Format tanggal kunjungan tidak valid.',
            'jam_kunjungan.date_format' => 'Format jam harus HH:MM.',
            'keluhan.max'               => 'Keluhan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}