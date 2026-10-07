<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $table = 'rekam_medis';

    protected $fillable = [
        'pendaftaran_id', 'diagnosa', 'tindakan', 'resep', 'catatan',
    ];

    public function pendaftaran() { return $this->belongsTo(Pendaftaran::class); }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('diagnosa', 'like', "%{$keyword}%")
              ->orWhere('tindakan', 'like', "%{$keyword}%");
        });
    }
}
