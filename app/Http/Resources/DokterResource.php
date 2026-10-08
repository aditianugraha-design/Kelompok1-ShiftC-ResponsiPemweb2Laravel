<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DokterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nip' => $this->nip,
            'spesialisasi' => $this->spesialisasi,
            'no_telepon' => $this->no_telepon,
            'status' => $this->status,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,
            'jadwal_praktik' => $this->jadwal_praktik,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
