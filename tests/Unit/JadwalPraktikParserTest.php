<?php

namespace Tests\Unit;

use App\Services\JadwalPraktikParser;
use PHPUnit\Framework\TestCase;

class JadwalPraktikParserTest extends TestCase
{
    public function test_rentang_hari_dan_satu_slot(): void
    {
        $hasil = JadwalPraktikParser::parse('Senin - Jumat, 08:00 - 14:00');

        $this->assertTrue($hasil['parseable']);
        $this->assertSame([1, 2, 3, 4, 5], $hasil['days']);
        $this->assertSame([['08:00', '14:00']], $hasil['slots']);
    }

    public function test_daftar_hari_dan_multi_slot(): void
    {
        $hasil = JadwalPraktikParser::parse('Senin, Rabu & Jumat, 08:00 - 12:00 & 14:00 - 17:00');

        $this->assertTrue($hasil['parseable']);
        $this->assertSame([1, 3, 5], $hasil['days']);
        $this->assertSame([['08:00', '12:00'], ['14:00', '17:00']], $hasil['slots']);
    }

    public function test_teks_aneh_tidak_parseable(): void
    {
        $hasil = JadwalPraktikParser::parse('sesuai perjanjian');

        $this->assertFalse($hasil['parseable']);
    }

    public function test_cek_hari_dan_jam(): void
    {
        // 2026-10-14 adalah Rabu
        $this->assertTrue(JadwalPraktikParser::hariSesuai('2026-10-14', [1, 3, 5]));
        $this->assertFalse(JadwalPraktikParser::hariSesuai('2026-10-15', [1, 3, 5]));

        $this->assertTrue(JadwalPraktikParser::jamSesuai('09:00', [['08:00', '12:00']]));
        $this->assertFalse(JadwalPraktikParser::jamSesuai('13:00', [['08:00', '12:00']]));
        $this->assertTrue(JadwalPraktikParser::jamSesuai('15:00', [['08:00', '12:00'], ['14:00', '17:00']]));
    }
}
