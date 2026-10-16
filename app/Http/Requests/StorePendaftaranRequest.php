<?php

namespace App\Http\Requests;

use App\Models\Dokter;
use App\Services\JadwalPraktikParser;
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $dokter = Dokter::find($this->input('dokter_id'));

            if (! $dokter) {
                return;
            }

            if ($dokter->status !== 'aktif') {
                $validator->errors()->add('dokter_id', 'Dokter yang dipilih sedang tidak aktif (non-aktif).');

                return;
            }

            $jadwal = JadwalPraktikParser::parse($dokter->jadwal_praktik);

            if (! $jadwal['parseable']) {
                return;
            }

            $tgl = (string) $this->input('tgl_kunjungan');
            $jam = substr((string) $this->input('jam_kunjungan'), 0, 5);

            if (! JadwalPraktikParser::hariSesuai($tgl, $jadwal['days'])) {
                $hari = JadwalPraktikParser::namaHari($jadwal['days']);
                $validator->errors()->add(
                    'tgl_kunjungan',
                    "Dokter {$dokter->nama} hanya praktik pada hari: {$hari}."
                );
            }

            if (! JadwalPraktikParser::jamSesuai($jam, $jadwal['slots'])) {
                $validator->errors()->add(
                    'jam_kunjungan',
                    "Jam {$jam} di luar jadwal praktik dokter ({$dokter->jadwal_praktik})."
                );
            }
        });
    }
}