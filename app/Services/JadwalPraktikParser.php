<?php

namespace App\Services;

/**
 * Parser jadwal praktik dokter dari teks bebas (Bahasa Indonesia)
 * menjadi hari + slot jam yang bisa divalidasi mesin.
 *
 * Contoh yang didukung:
 * - 'Senin - Jumat, 08:00 - 14:00' => hari 1-5, slot [[08:00,14:00]]
 * - 'Senin, Rabu & Jumat, 08:00 - 12:00 & 14:00 - 17:00'
 *   => hari [1,3,5], slot [[08:00,12:00],[14:00,17:00]]
 * - 'Selasa & Kamis, 13:00 - 17:00' => hari [2,4], slot [[13:00,17:00]]
 *
 * Jika teks tidak bisa dipahami (tidak ada hari terdeteksi),
 * parseable = false dan validasi dilewati (fail-open) agar data
 * lama yang formatnya aneh tidak mengunci pendaftaran.
 */
class JadwalPraktikParser
{
    public const HARI = [
        'senin' => 1,
        'selasa' => 2,
        'rabu' => 3,
        'kamis' => 4,
        'jumat' => 5,
        "jum'at" => 5,
        'sabtu' => 6,
        'minggu' => 7,
    ];

    public const NAMA_HARI = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    /**
     * @return array{days: array<int, int>, slots: array<int, array{0: string, 1: string}>, parseable: bool}
     */
    public static function parse(?string $teks): array
    {
        $normal = mb_strtolower(trim((string) $teks));

        if ($normal === '') {
            return ['days' => [], 'slots' => [], 'parseable' => false];
        }

        $days = self::parseHari($normal);
        $slots = self::parseSlot($normal);

        if ($days === []) {
            return ['days' => [], 'slots' => $slots, 'parseable' => false];
        }

        return ['days' => $days, 'slots' => $slots, 'parseable' => true];
    }

    /**
     * @return array<int, int>
     */
    private static function parseHari(string $normal): array
    {
        // Rentang: 'senin - jumat'
        if (preg_match('/(senin|selasa|rabu|kamis|juma\'?t|sabtu|minggu)\s*-\s*(senin|selasa|rabu|kamis|juma\'?t|sabtu|minggu)/', $normal, $m)) {
            $dari = self::HARI[$m[1]];
            $sampai = self::HARI[$m[2]];
            $days = [];
            for ($d = $dari; $d <= $sampai; $d++) {
                $days[] = $d;
            }

            return $days;
        }

        // Daftar: 'senin, rabu & jumat' — kumpulkan semua nama hari yang disebut
        $days = [];
        foreach (self::HARI as $nama => $angka) {
            if (preg_match('/\b'.preg_quote($nama, '/').'\b/u', $normal)) {
                $days[] = $angka;
            }
        }

        sort($days);

        return array_values(array_unique($days));
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    private static function parseSlot(string $normal): array
    {
        preg_match_all('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $normal, $m, PREG_SET_ORDER);

        $slots = [];
        foreach ($m as $pasangan) {
            $mulai = self::normalisasiJam($pasangan[1]);
            $selesai = self::normalisasiJam($pasangan[2]);

            if ($mulai !== null && $selesai !== null && $mulai < $selesai) {
                $slots[] = [$mulai, $selesai];
            }
        }

        return $slots;
    }

    private static function normalisasiJam(string $jam): ?string
    {
        [$h, $i] = array_map('intval', explode(':', $jam));

        if ($h < 0 || $h > 23 || $i < 0 || $i > 59) {
            return null;
        }

        return sprintf('%02d:%02d', $h, $i);
    }

    public static function hariSesuai(string $tanggal, array $days): bool
    {
        if ($days === []) {
            return true;
        }

        $nomor = (int) date('N', strtotime($tanggal));

        return in_array($nomor, $days, true);
    }

    public static function jamSesuai(string $jam, array $slots): bool
    {
        if ($slots === []) {
            return true;
        }

        $t = substr($jam, 0, 5);

        foreach ($slots as [$mulai, $selesai]) {
            if ($t >= $mulai && $t <= $selesai) {
                return true;
            }
        }

        return false;
    }

    public static function namaHari(array $days): string
    {
        return implode(', ', array_map(fn ($d) => self::NAMA_HARI[$d] ?? '-', $days));
    }
}
