<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        return view('dokter.index', $this->viewData($request));
    }

    public function create(Request $request)
    {
        return view('dokter.index', array_merge($this->viewData($request), [
            'openCreate' => true,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('dokters', 'nip')],
            'spesialisasi' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'jadwal_praktik' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        Dokter::create($data);

        return redirect()->route('dokter.index')->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function edit(Request $request, Dokter $dokter)
    {
        return view('dokter.index', array_merge($this->viewData($request), [
            'openEdit' => $dokter,
        ]));
    }

    public function update(Request $request, Dokter $dokter)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('dokters', 'nip')->ignore($dokter->id)],
            'spesialisasi' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'jadwal_praktik' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,non-aktif',
        ]);

        $dokter->update($data);

        return redirect()->route('dokter.index')->with('success', 'Data dokter berhasil diperbarui.');
    }

    public function destroy(Dokter $dokter)
    {
        $dokter->delete();

        return redirect()->route('dokter.index')->with('success', 'Dokter berhasil dihapus.');
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
