<?php

namespace App\Http\Requests;

use App\Models\Dokter;
use App\Models\Pendaftaran;
use App\Services\JadwalPraktikParser;
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
            'status' => ['required', Rule::in(['menunggu', 'diproses', 'batal'])],
            'dokter_id' => 'sometimes|nullable|exists:dokters,id',
            'tgl_kunjungan' => 'sometimes|nullable|date',
            'jam_kunjungan' => 'sometimes|nullable|date_format:H:i',
            'keluhan' => 'sometimes|nullable|string|max:1000',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $pendaftaran = $this->route('pendaftaran');

            if (! $pendaftaran instanceof Pendaftaran) {
                return;
            }

            $to = $this->input('status');

            if ($to === null || $to === '') {
                return;
            }

            if ($to === $pendaftaran->status) {
                return;
            }

            if ($to === Pendaftaran::STATUS_SELESAI) {
                $validator->errors()->add('status', 'Status selesai hanya dapat dibuat melalui rekam medis.');

                return;
            }

            if (! $pendaftaran->canTransitionTo($to)) {
                $validator->errors()->add('status', "Transisi status dari {$pendaftaran->status} ke {$to} tidak diizinkan.");
            }

            $dokterId = $this->input('dokter_id', $pendaftaran->dokter_id);
            $tgl = $this->input('tgl_kunjungan', $pendaftaran->tgl_kunjungan?->toDateString());
            $jamInput = $this->input('jam_kunjungan', is_string($pendaftaran->jam_kunjungan) ? $pendaftaran->jam_kunjungan : null);

            if ($dokterId && $tgl && $jamInput) {
                $dokter = Dokter::find($dokterId);

                if ($dokter && $dokter->status === 'aktif') {
                    $jadwal = JadwalPraktikParser::parse($dokter->jadwal_praktik);

                    if ($jadwal['parseable']) {
                        $jam = substr((string) $jamInput, 0, 5);

                        if (! JadwalPraktikParser::hariSesuai((string) $tgl, $jadwal['days'])) {
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
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status pendaftaran wajib diisi.',
            'status.in' => 'Status tidak valid. Pilih salah satu: menunggu, diproses, atau batal. Status selesai hanya via rekam medis.',
            'dokter_id.exists' => 'Dokter yang dipilih tidak ditemukan.',
            'tgl_kunjungan.date' => 'Format tanggal kunjungan tidak valid.',
            'jam_kunjungan.date_format' => 'Format jam harus HH:MM.',
            'keluhan.max' => 'Keluhan tidak boleh lebih dari 1000 karakter.',
        ];
    }
}