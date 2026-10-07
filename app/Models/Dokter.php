<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'nama', 'no_sip', 'spesialisasi',
        'no_telp', 'foto', 'jadwal_praktik',
    ];

    protected $casts = ['jadwal_praktik' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function pendaftarans() { return $this->hasMany(Pendaftaran::class); }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
              ->orWhere('no_sip', 'like', "%{$keyword}%")
              ->orWhere('spesialisasi', 'like', "%{$keyword}%");
        });
    }
}