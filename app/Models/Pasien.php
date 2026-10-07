<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pasien extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'nik', 'nama', 'tgl_lahir',
        'jenis_kelamin', 'alamat', 'no_telp', 'golongan_darah',
    ];

    protected $casts = ['tgl_lahir' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function pendaftarans() { return $this->hasMany(Pendaftaran::class); }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
              ->orWhere('nik', 'like', "%{$keyword}%")
              ->orWhere('no_telp', 'like', "%{$keyword}%");
        });
    }
}
