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
            'no_sip' => $this->no_sip,
            'spesialisasi' => $this->spesialisasi,
            'no_telp' => $this->no_telp,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,
            'jadwal_praktik' => $this->jadwal_praktik,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
