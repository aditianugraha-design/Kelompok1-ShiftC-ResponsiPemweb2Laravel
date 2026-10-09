<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePendaftaranRequest;
use App\Http\Requests\UpdatePendaftaranRequest;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
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
            // Dokter melihat antrean pasien yang mendaftar ke dirinya (hari ini)
            if ($user->dokter) {
                $query->where('dokter_id', $user->dokter->id);
                // Default tampilkan hari ini jika tidak ada filter tanggal
                if (!$request->filled('tgl_kunjungan')) {
                    $query->whereDate('tgl_kunjungan', today());
                }
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

        $pendaftarans = $query->latest('tgl_kunjungan')->latest('id')->paginate(10)->withQueryString();

        // Statistik Counter (sesuaikan scope)
        $statsQuery = Pendaftaran::query();
        if ($user->isPasien() && $user->pasien) {
            $statsQuery->where('pasien_id', $user->pasien->id);
        } elseif ($user->isDokter() && $user->dokter) {
            $statsQuery->where('dokter_id', $user->dokter->id)->whereDate('tgl_kunjungan', today());
        }

        $total          = (clone $statsQuery)->count();
        $totalMenunggu  = (clone $statsQuery)->where('status', 'menunggu')->count();
        $totalDiproses  = (clone $statsQuery)->where('status', 'diproses')->count();
        $totalSelesai   = (clone $statsQuery)->where('status', 'selesai')->count();
        $totalBatal     = (clone $statsQuery)->where('status', 'batal')->count();

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
            'pasiens'
        ));
    }

    public function create()
    {
        $user = auth()->user();

        // Pasien: cek apakah sudah memiliki profil pasien
        if ($user->isPasien() && !$user->pasien) {
            return redirect()->route('pasien.complete-profile')
                ->with('info', 'Lengkapi data profil pasien Anda terlebih dahulu sebelum membuat pendaftaran.');
        }

        $dokters = Dokter::where('status', 'aktif')->orderBy('nama', 'asc')->get();

        // Admin bisa pilih pasien mana saja; pasien otomatis dirinya sendiri
        $pasiens = $user->isAdmin() ? Pasien::orderBy('nama', 'asc')->get() : collect();
        $pasienSelf = $user->isPasien() ? $user->pasien : null;

        return view('pendaftaran.create', compact('dokters', 'pasiens', 'pasienSelf'));
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
        $pendaftaran->update($request->validated());

        return redirect()->route('pendaftaran.index')
            ->with('success', "Pendaftaran {$pendaftaran->kode_daftar} berhasil diperbarui.");
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        $user = auth()->user();

        // Hanya admin yang bisa menghapus pendaftaran
        if (!$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data pendaftaran.');
        }

        $kode = $pendaftaran->kode_daftar;
        $pendaftaran->delete();

        return redirect()->route('pendaftaran.index')
            ->with('success', "Data pendaftaran {$kode} berhasil dihapus.");
    }
}