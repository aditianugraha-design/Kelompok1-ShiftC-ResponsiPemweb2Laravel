<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'nama', 'nip', 'spesialisasi',
        'no_telepon', 'foto', 'jadwal_praktik', 'status',
    ];

    protected $attributes = [
        'status' => 'aktif',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function pendaftarans() { return $this->hasMany(Pendaftaran::class); }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
              ->orWhere('nip', 'like', "%{$keyword}%")
              ->orWhere('spesialisasi', 'like', "%{$keyword}%");
        });
    }
}