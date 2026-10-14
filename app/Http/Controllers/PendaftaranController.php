<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePendaftaranRequest;
use App\Http\Requests\UpdatePendaftaranRequest;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Services\JadwalPraktikParser;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Pendaftaran::with(['pasien', 'dokter', 'rekamMedis']);

        // ===== OTORISASI BERBASIS ROLE =====
        if ($user->isPasien()) {
            // Pasien hanya melihat riwayat pendaftarannya sendiri
            if ($user->pasien) {
                $query->where('pasien_id', $user->pasien->id);
            } else {
                // Belum punya data pasien, tampilkan kosong
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->isDokter()) {
            // Dokter melihat antrean pasien yang mendaftar ke dirinya (semua tanggal).
            // Filter tanggal hanya berlaku jika user memilih manual, agar sama dengan dashboard.
            if ($user->dokter) {
                $query->where('dokter_id', $user->dokter->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        // Admin: tidak ada batasan tambahan — tampilkan semua

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_daftar', 'like', "%{$search}%")
                    ->orWhere('keluhan', 'like', "%{$search}%")
                    ->orWhereHas('pasien', function ($qp) use ($search) {
                        $qp->where('nama', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dokter', function ($qd) use ($search) {
                        $qd->where('nama', 'like', "%{$search}%")
                            ->orWhere('spesialisasi', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Dokter (hanya untuk Admin)
        if ($request->filled('dokter_id') && ($user->isAdmin())) {
            $query->where('dokter_id', $request->dokter_id);
        }

        // Filter Tanggal Kunjungan
        if ($request->filled('tgl_kunjungan')) {
            $query->whereDate('tgl_kunjungan', $request->tgl_kunjungan);
        }

        // Antrean = hari ini: default tampilkan kunjungan hari ini untuk admin & dokter.
        // Pasien tetap melihat seluruh riwayatnya. Filter tanggal manual tetap bisa pilih hari lain.
        $tanggalAktif = $request->input('tgl_kunjungan');
        if (! $user->isPasien() && ! $request->filled('tgl_kunjungan')) {
            $tanggalAktif = today()->toDateString();
            $query->whereDate('tgl_kunjungan', today());
        }

        if ($user->isPasien()) {
            $pendaftarans = $query->latest('tgl_kunjungan')->latest('id')->paginate(10)->withQueryString();
        } else {
            // Urut antrean dari jam paling awal agar pasien terdepan diperiksa dulu
            $pendaftarans = $query->orderBy('jam_kunjungan', 'asc')->orderBy('id', 'asc')->paginate(10)->withQueryString();
        }

        // Statistik Counter (sesuaikan scope + tanggal yang sedang ditampilkan)
        $statsQuery = Pendaftaran::query();
        if ($user->isPasien() && $user->pasien) {
            $statsQuery->where('pasien_id', $user->pasien->id);
        } elseif ($user->isDokter() && $user->dokter) {
            $statsQuery->where('dokter_id', $user->dokter->id);
        }

        if (! $user->isPasien() && $tanggalAktif) {
            $statsQuery->whereDate('tgl_kunjungan', $tanggalAktif);
        }

        $total = (clone $statsQuery)->count();
        $totalMenunggu = (clone $statsQuery)->where('status', 'menunggu')->count();
        $totalDiproses = (clone $statsQuery)->where('status', 'diproses')->count();
        $totalSelesai = (clone $statsQuery)->where('status', 'selesai')->count();
        $totalBatal = (clone $statsQuery)->where('status', 'batal')->count();

        // Data untuk Dropdown (hanya dibutuhkan Admin)
        $dokters = $user->isAdmin() ? Dokter::orderBy('nama', 'asc')->get() : collect();
        $pasiens = $user->isAdmin() ? Pasien::orderBy('nama', 'asc')->get() : collect();

        return view('pendaftaran.index', compact(
            'pendaftarans',
            'total',
            'totalMenunggu',
            'totalDiproses',
            'totalSelesai',
            'totalBatal',
            'dokters',
            'pasiens',
            'tanggalAktif'
        ));
    }

    public function create()
    {
        $user = auth()->user();

        // Pasien: cek apakah sudah memiliki profil pasien
        if ($user->isPasien() && ! $user->pasien) {
            return redirect()->route('pasien.complete-profile')
                ->with('info', 'Lengkapi data profil pasien Anda terlebih dahulu sebelum membuat pendaftaran.');
        }

        $dokters = Dokter::where('status', 'aktif')->orderBy('nama', 'asc')->get();

        // Admin bisa pilih pasien mana saja; pasien otomatis dirinya sendiri
        $pasiens = $user->isAdmin() ? Pasien::orderBy('nama', 'asc')->get() : collect();
        $pasienSelf = $user->isPasien() ? $user->pasien : null;

        $jadwalMap = $dokters->mapWithKeys(fn ($d) => [
            $d->id => array_merge(
                JadwalPraktikParser::parse($d->jadwal_praktik),
                ['text' => $d->jadwal_praktik]
            ),
        ])->all();

        return view('pendaftaran.create', compact('dokters', 'pasiens', 'pasienSelf', 'jadwalMap'));
    }

    public function store(StorePendaftaranRequest $request)
    {
        $validated = $request->validated();
        $validated['kode_daftar'] = Pendaftaran::generateKodeDaftar();
        $validated['status'] = 'menunggu';

        $pendaftaran = Pendaftaran::create($validated);

        return redirect()->route('pendaftaran.index')
            ->with('success', "Pendaftaran berhasil dibuat dengan Kode: {$pendaftaran->kode_daftar}.");
    }

    public function update(UpdatePendaftaranRequest $request, Pendaftaran $pendaftaran)
    {
        $to = $request->input('status');

        if ($to === null || $to === '') {
            return redirect()->back()->withErrors(['status' => 'Status pendaftaran wajib diisi.']);
        }

        if (! $pendaftaran->canTransitionTo($to)) {
            return redirect()->back()->withErrors(['status' => "Transisi status dari {$pendaftaran->status} ke {$to} tidak diizinkan."]);
        }

        $pendaftaran->update($request->validated());

        return redirect()->route('pendaftaran.index')
            ->with('success', "Pendaftaran {$pendaftaran->kode_daftar} berhasil diperbarui menjadi {$pendaftaran->status}.");
    }

    public function cancel(Pendaftaran $pendaftaran)
    {
        $user = auth()->user();

        if (! $user->isPasien() || ! $user->pasien || $pendaftaran->pasien_id !== $user->pasien->id) {
            abort(403, 'Anda tidak memiliki akses ke data pendaftaran ini.');
        }

        if ($pendaftaran->status !== Pendaftaran::STATUS_MENUNGGU) {
            return redirect()->back()->withErrors(['status' => 'Hanya pendaftaran menunggu yang dapat dibatalkan.']);
        }

        $pendaftaran->update(['status' => Pendaftaran::STATUS_BATAL]);

        return redirect()->route('pendaftaran.index')
            ->with('success', "Pendaftaran {$pendaftaran->kode_daftar} berhasil dibatalkan.");
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        $user = auth()->user();

        // Hanya admin yang bisa menghapus pendaftaran
        if (! $user->isAdmin()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data pendaftaran.');
        }

        if ($pendaftaran->status !== Pendaftaran::STATUS_MENUNGGU) {
            return redirect()->back()->withErrors(['status' => 'Hanya pendaftaran menunggu yang bisa dihapus.']);
        }

        $kode = $pendaftaran->kode_daftar;
        $pendaftaran->delete();

        return redirect()->route('pendaftaran.index')
            ->with('success', "Data pendaftaran {$kode} berhasil dihapus.");
    }
}