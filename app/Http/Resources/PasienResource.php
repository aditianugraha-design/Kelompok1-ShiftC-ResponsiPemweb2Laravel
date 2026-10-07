<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PasienResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'tgl_lahir' => $this->tgl_lahir?->format('Y-m-d'),
            'jenis_kelamin' => $this->jenis_kelamin,
            'jenis_kelamin_label' => $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            'alamat' => $this->alamat,
            'no_telp' => $this->no_telp,
            'golongan_darah' => $this->golongan_darah,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

