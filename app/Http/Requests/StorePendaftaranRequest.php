<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        // Jika user adalah pasien, paksa pasien_id ke ID pasiennya sendiri
        $user = $this->user();
        if ($user && $user->isPasien() && $user->pasien) {
            $this->merge(['pasien_id' => $user->pasien->id]);
        }
    }

    public function rules(): array
    {
        return [
            'pasien_id'     => 'required|exists:pasiens,id',
            'dokter_id'     => 'required|exists:dokters,id',
            'tgl_kunjungan' => 'required|date|after_or_equal:today',
            'jam_kunjungan' => 'required|date_format:H:i',
            'keluhan'       => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'pasien_id.required'        => 'Pasien wajib dipilih.',
            'pasien_id.exists'          => 'Data pasien tidak ditemukan.',
            'dokter_id.required'        => 'Dokter pemeriksa wajib dipilih.',
            'dokter_id.exists'          => 'Data dokter tidak ditemukan.',
            'tgl_kunjungan.required'    => 'Tanggal kunjungan wajib diisi.',
            'tgl_kunjungan.date'        => 'Format tanggal kunjungan tidak valid.',
            'tgl_kunjungan.after_or_equal' => 'Tanggal kunjungan tidak boleh di masa lalu.',
            'jam_kunjungan.required'    => 'Jam kunjungan wajib diisi.',
            'jam_kunjungan.date_format' => 'Format jam kunjungan harus HH:MM (contoh: 09:00).',
            'keluhan.required'          => 'Keluhan utama wajib diisi.',
            'keluhan.max'               => 'Keluhan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}