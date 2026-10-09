<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRekamMedisRequest;
use App\Http\Requests\UpdateRekamMedisRequest;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekamMedisController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = RekamMedis::with(['pendaftaran.pasien', 'pendaftaran.dokter']);

        // Filter berdasarkan role
        if ($user->isDokter()) {
            $dokter = Dokter::where('user_id', $user->id)->first();
            $query->whereHas('pendaftaran', fn($q) => $q->where('dokter_id', $dokter?->id));
        } elseif ($user->isPasien()) {
            $pasien = Pasien::where('user_id', $user->id)->first();
            $query->whereHas('pendaftaran', fn($q) => $q->where('pasien_id', $pasien?->id));
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $rekamMedis = $query->latest()->paginate(10)->withQueryString();

        return view('rekam-medis.index', compact('rekamMedis'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        $query = Pendaftaran::with(['pasien', 'dokter'])
            ->whereIn('status', ['menunggu', 'diproses'])
            ->whereDoesntHave('rekamMedis');

        if ($user->isDokter()) {
            $dokter = Dokter::where('user_id', $user->id)->first();
            $query->where('dokter_id', $dokter?->id);
        }

        $pendaftarans = $query->latest()->get();

        $selectedPendaftaran = null;
        if ($request->filled('pendaftaran_id')) {
            $selectedPendaftaran = Pendaftaran::find($request->pendaftaran_id);
        }

        return view('rekam-medis.create', compact('pendaftarans', 'selectedPendaftaran'));
    }

    public function store(StoreRekamMedisRequest $request)
    {
        DB::transaction(function () use ($request) {
            $rekamMedis = RekamMedis::create($request->validated());

            // Business process: update status pendaftaran jadi selesai
            $rekamMedis->pendaftaran->update(['status' => 'selesai']);
        });

        return redirect()->route('rekam-medis.index')
            ->with('success', 'Rekam medis berhasil disimpan. Status pendaftaran telah diubah menjadi Selesai.');
    }

    public function show(RekamMedis $rekamMedis)
    {
        $rekamMedis->load(['pendaftaran.pasien', 'pendaftaran.dokter']);
        return view('rekam-medis.show', compact('rekamMedis'));
    }

    public function edit(RekamMedis $rekamMedis)
    {
        $rekamMedis->load(['pendaftaran.pasien', 'pendaftaran.dokter']);
        return view('rekam-medis.edit', compact('rekamMedis'));
    }

    public function update(UpdateRekamMedisRequest $request, RekamMedis $rekamMedis)
    {
        $rekamMedis->update($request->validated());

        return redirect()->route('rekam-medis.show', $rekamMedis)
            ->with('success', 'Rekam medis berhasil diperbarui.');
    }

    public function destroy(RekamMedis $rekamMedis)
    {
        DB::transaction(function () use ($rekamMedis) {
            // Balikin status pendaftaran ke diproses
            $rekamMedis->pendaftaran->update(['status' => 'diproses']);
            $rekamMedis->delete();
        });

        return redirect()->route('rekam-medis.index')
            ->with('success', 'Rekam medis berhasil dihapus. Status pendaftaran dikembalikan ke Diproses.');
    }
}

