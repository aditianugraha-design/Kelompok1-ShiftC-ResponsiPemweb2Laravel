<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_daftar', 'pasien_id', 'dokter_id',
        'tgl_kunjungan', 'jam_kunjungan', 'keluhan', 'status',
    ];

    protected $casts = ['tgl_kunjungan' => 'date'];

    public function pasien() { return $this->belongsTo(Pasien::class); }
    public function dokter() { return $this->belongsTo(Dokter::class); }
    public function rekamMedis() { return $this->hasOne(RekamMedis::class); }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('kode_daftar', 'like', "%{$keyword}%")
              ->orWhere('keluhan', 'like', "%{$keyword}%");
        });
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public static function generateKodeDaftar(): string
    {
        $prefix = 'REG-' . date('Ymd');
        $last = self::where('kode_daftar', 'like', $prefix . '%')
            ->orderBy('kode_daftar', 'desc')
            ->first();

        $number = $last ? (int) substr($last->kode_daftar, -4) + 1 : 1;

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
