<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['pasien', 'dokter', 'rekamMedis']);

        // Jika user adalah pasien dan memiliki data pasien terkait, tampilkan riwayat pendaftarannya
        // atau jika tidak, tampilkan seluruh pendaftaran (misal untuk admin/dokter/staff)
        $user = auth()->user();
        if ($user && $user->role === 'pasien' && $user->pasien) {
            // Bisa difilter per pasien jika diinginkan, namun jika ingin melihat pendaftaran miliknya
            // $query->where('pasien_id', $user->pasien->id);
            // Tapi agar pengguna bisa melihat data contoh responsi jika belum terhubung, kita biarkan fleksibel:
        }

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

        // Filter Dokter
        if ($request->filled('dokter_id')) {
            $query->where('dokter_id', $request->dokter_id);
        }

        // Filter Tanggal Kunjungan
        if ($request->filled('tgl_kunjungan')) {
            $query->whereDate('tgl_kunjungan', $request->tgl_kunjungan);
        }

        $pendaftarans = $query->latest('tgl_kunjungan')->latest('id')->paginate(10)->withQueryString();

        // Statistik Counter
        $total = Pendaftaran::count();
        $totalMenunggu = Pendaftaran::where('status', 'menunggu')->count();
        $totalDiproses = Pendaftaran::where('status', 'diproses')->count();
        $totalSelesai = Pendaftaran::where('status', 'selesai')->count();
        $totalBatal = Pendaftaran::where('status', 'batal')->count();

        // Data untuk Dropdown Modal & Filter
        $dokters = Dokter::orderBy('nama', 'asc')->get();
        $pasiens = Pasien::orderBy('nama', 'asc')->get();

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
        $dokters = Dokter::orderBy('nama', 'asc')->get();
        $pasiens = Pasien::orderBy('nama', 'asc')->get();

        return view('pendaftaran.create', compact('dokters', 'pasiens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'dokter_id' => 'required|exists:dokters,id',
            'tgl_kunjungan' => 'required|date',
            'jam_kunjungan' => 'required',
            'keluhan' => 'required|string|max:1000',
        ], [
            'pasien_id.required' => 'Pilih pasien yang akan didaftarkan.',
            'dokter_id.required' => 'Pilih dokter pemeriksa.',
            'tgl_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'jam_kunjungan.required' => 'Jam kunjungan wajib ditentukan.',
            'keluhan.required' => 'Keluhan utama pasien wajib diisi.',
        ]);

        $validated['kode_daftar'] = Pendaftaran::generateKodeDaftar();
        $validated['status'] = 'menunggu';

        $pendaftaran = Pendaftaran::create($validated);

        return redirect()->route('pendaftaran.index')
            ->with('success', "Pendaftaran berhasil dibuat dengan Kode: {$pendaftaran->kode_daftar}.");
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['menunggu', 'diproses', 'selesai', 'batal'])],
            'dokter_id' => 'sometimes|required|exists:dokters,id',
            'tgl_kunjungan' => 'sometimes|required|date',
            'jam_kunjungan' => 'sometimes|required',
            'keluhan' => 'sometimes|required|string|max:1000',
        ], [
            'status.in' => 'Status pendaftaran tidak valid.',
            'dokter_id.exists' => 'Dokter yang dipilih tidak ditemukan.',
        ]);

        $pendaftaran->update($validated);

        return redirect()->route('pendaftaran.index')
            ->with('success', "Pendaftaran {$pendaftaran->kode_daftar} berhasil diperbarui.");
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        $kode = $pendaftaran->kode_daftar;
        $pendaftaran->delete();

        return redirect()->route('pendaftaran.index')
            ->with('success', "Data pendaftaran {$kode} berhasil dihapus.");
    }
}
