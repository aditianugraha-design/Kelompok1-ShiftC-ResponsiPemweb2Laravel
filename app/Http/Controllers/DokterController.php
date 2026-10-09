<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDokterRequest;
use App\Http\Requests\UpdateDokterRequest;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        return view('dokter.index', $this->viewData($request));
    }

    public function create(Request $request)
    {
        $this->authorizeAdmin();

        return view('dokter.index', array_merge($this->viewData($request), [
            'openCreate' => true,
        ]));
    }

    public function store(StoreDokterRequest $request)
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'aktif';

        $dokter = Dokter::create($data);

        return redirect()->route('dokter.index')->with('success', "Dokter {$dokter->nama} berhasil ditambahkan.");
    }

    public function edit(Request $request, Dokter $dokter)
    {
        $this->authorizeAdmin();

        return view('dokter.index', array_merge($this->viewData($request), [
            'openEdit' => $dokter,
        ]));
    }

    public function update(UpdateDokterRequest $request, Dokter $dokter)
    {
        $data = $request->validated();
        $dokter->update($data);

        return redirect()->route('dokter.index')->with('success', "Data dokter {$dokter->nama} berhasil diperbarui.");
    }

    public function destroy(Dokter $dokter)
    {
        $this->authorizeAdmin();

        $nama = $dokter->nama;
        $dokter->delete();

        return redirect()->route('dokter.index')->with('success', "Data dokter {$nama} berhasil dihapus.");
    }

    /**
     * Pastikan pengguna saat ini memiliki hak akses Administrator.
     */
    protected function authorizeAdmin(): void
    {
        $user = auth()->user();
        if (! $user || (! $user->hasRole('admin') && $user->role !== 'admin')) {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Administrator.');
        }
    }

    /**
     * Siapkan data daftar, statistik, dan filter untuk halaman manajemen dokter.
     */
    protected function viewData(Request $request): array
    {
        $query = Dokter::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('spesialisasi')) {
            $query->where('spesialisasi', $request->spesialisasi);
        }

        $dokters = $query->latest()->paginate(10)->withQueryString();

        $total = Dokter::count();
        $totalSpesialis = Dokter::where('spesialisasi', '!=', 'Umum')->count();
        $totalUmum = Dokter::where('spesialisasi', 'Umum')->count();
        $totalAktif = Dokter::where('status', 'aktif')->count();

        $spesialisasiList = collect([
            'Umum', 'Penyakit Dalam', 'Anak', 'Kandungan', 'Gigi', 'Mata', 'THT',
            'Kulit & Kelamin', 'Jantung', 'Saraf', 'Orthopedi', 'Radiologi',
        ])
            ->merge(
                Dokter::query()
                    ->whereNotNull('spesialisasi')
                    ->where('spesialisasi', '!=', '')
                    ->distinct()
                    ->pluck('spesialisasi')
            )
            ->unique()
            ->values();

        if ($request->filled('spesialisasi') && ! $spesialisasiList->contains($request->spesialisasi)) {
            $spesialisasiList->push($request->spesialisasi);
        }

        return compact('dokters', 'total', 'totalSpesialis', 'totalUmum', 'totalAktif', 'spesialisasiList');
    }
}
