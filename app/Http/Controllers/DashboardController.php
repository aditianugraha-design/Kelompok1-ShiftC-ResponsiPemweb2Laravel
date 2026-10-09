<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        [$stats, $recentPendaftaran] = match (true) {
            $user->isAdmin()  => $this->dataAdmin(),
            $user->isDokter() => $this->dataDokter($user->dokter),
            default           => $this->dataPasien($user->pasien),
        };

        return view('dashboard', compact('stats', 'recentPendaftaran', 'user'));
    }

    // ===== ADMIN =====
    private function dataAdmin(): array
    {
        $stats = [
            'total_pasien'         => Pasien::count(),
            'total_dokter'         => Dokter::where('status', 'aktif')->count(),
            'antrean_aktif'        => Pendaftaran::whereIn('status', ['menunggu', 'diproses'])->count(),
            'selesai_hari_ini'     => Pendaftaran::where('status', 'selesai')
                                        ->whereDate('updated_at', today())->count(),
            'pendaftaran_hari_ini' => Pendaftaran::whereDate('tgl_kunjungan', today())->count(),
            'total_rekam_medis'    => RekamMedis::count(),
        ];

        $recent = Pendaftaran::with(['pasien', 'dokter'])
            ->latest()
            ->latest('id')
            ->take(5)
            ->get();

        return [$stats, $recent];
    }

    // ===== DOKTER =====
    private function dataDokter(?Dokter $dokter): array
    {
        if (! $dokter) {
            return [
                [
                    'antrean_hari_ini'       => 0,
                    'menunggu'               => 0,
                    'diproses'               => 0,
                    'selesai_hari_ini'       => 0,
                    'total_pasien_ditangani' => 0,
                    'total_rekam_medis'      => 0,
                ],
                collect(), // jangan query dengan dokter_id = null
            ];
        }

        $hariIni = Pendaftaran::where('dokter_id', $dokter->id)
            ->whereDate('tgl_kunjungan', today());

        $perStatus = $this->hitungPerStatus(clone $hariIni);

        $stats = [
            'antrean_hari_ini'       => $perStatus->sum(),
            'menunggu'               => $perStatus->get('menunggu', 0),
            'diproses'               => $perStatus->get('diproses', 0),
            'selesai_hari_ini'       => $perStatus->get('selesai', 0),
            'total_pasien_ditangani' => Pendaftaran::where('dokter_id', $dokter->id)
                                            ->where('status', 'selesai')->count(),
            'total_rekam_medis'      => RekamMedis::whereHas('pendaftaran',
                                            fn (Builder $q) => $q->where('dokter_id', $dokter->id)
                                        )->count(),
        ];

        $recent = (clone $hariIni)
            ->with('pasien')
            ->orderBy('jam_kunjungan')
            ->take(5)
            ->get();

        return [$stats, $recent];
    }

    // ===== PASIEN =====
    private function dataPasien(?Pasien $pasien): array
    {
        if (! $pasien) {
            return [
                [
                    'total_kunjungan'    => 0,
                    'menunggu'           => 0,
                    'diproses'           => 0,
                    'selesai'            => 0,
                    'total_rekam_medis'  => 0,
                    'kunjungan_terakhir' => null,
                ],
                collect(), // jangan query dengan pasien_id = null
            ];
        }

        $base = Pendaftaran::where('pasien_id', $pasien->id);

        $perStatus = $this->hitungPerStatus(clone $base);

        $riwayat = (clone $base)
            ->with('dokter')
            ->latest('tgl_kunjungan')
            ->latest('id')
            ->take(5)
            ->get();

        $stats = [
            'total_kunjungan'    => $perStatus->sum(),
            'menunggu'           => $perStatus->get('menunggu', 0),
            'diproses'           => $perStatus->get('diproses', 0),
            'selesai'            => $perStatus->get('selesai', 0),
            'total_rekam_medis'  => RekamMedis::whereHas('pendaftaran',
                                        fn (Builder $q) => $q->where('pasien_id', $pasien->id)
                                    )->count(),
            // sudah ada di $riwayat, tidak perlu query tambahan
            'kunjungan_terakhir' => $riwayat->first(),
        ];

        return [$stats, $riwayat];
    }

    /**
     * Hitung jumlah per status dengan 1 query.
     * Hasil: ['menunggu' => 3, 'selesai' => 10, ...]
     */
    private function hitungPerStatus(Builder $query): Collection
    {
        return $query
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }
}
