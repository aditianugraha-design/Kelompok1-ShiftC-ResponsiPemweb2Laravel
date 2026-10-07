<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendaftaranResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_daftar' => $this->kode_daftar,
            'pasien' => new PasienResource($this->whenLoaded('pasien')),
            'dokter' => new DokterResource($this->whenLoaded('dokter')),
            'rekam_medis' => new RekamMedisResource($this->whenLoaded('rekamMedis')),
            'tgl_kunjungan' => $this->tgl_kunjungan?->format('Y-m-d'),
            'jam_kunjungan' => $this->jam_kunjungan,
            'keluhan' => $this->keluhan,
            'status' => $this->status,
            'status_label' => ucfirst($this->status),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
