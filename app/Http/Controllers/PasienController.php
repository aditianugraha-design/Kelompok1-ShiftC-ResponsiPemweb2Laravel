<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePasienRequest;
use App\Http\Requests\UpdatePasienRequest;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user && ($user->role === 'pasien' || $user->hasRole('pasien'))) {
            return redirect()->route('pasien.profile');
        }

        $query = Pasien::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $pasiens = $query->latest()->paginate(10)->withQueryString();
        $total = Pasien::count();
        $totalLaki = Pasien::where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Pasien::where('jenis_kelamin', 'P')->count();

        return view('pasien.index', compact('pasiens', 'total', 'totalLaki', 'totalPerempuan'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('pasien.create');
    }

    public function store(StorePasienRequest $request)
    {
        Pasien::create($request->validated());

        return redirect()->route('pasien.index')->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function show(Pasien $pasien)
    {
        $pendaftarans = $pasien->pendaftarans()
            ->with(['dokter', 'rekamMedis'])
            ->latest('tgl_kunjungan')
            ->latest('id')
            ->get();

        return view('pasien.show', compact('pasien', 'pendaftarans'));
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $pasien = $user->pasien;

        if (! $pasien) {
            return redirect()->route('pasien.complete-profile');
        }

        $pendaftarans = $pasien->pendaftarans()
            ->with(['dokter', 'rekamMedis'])
            ->latest('tgl_kunjungan')
            ->latest('id')
            ->get();

        return view('pasien.profile', compact('user', 'pasien', 'pendaftarans'));
    }

    public function completeProfile(Request $request)
    {
        $pasien = $request->user()->pasien;

        return view('pasien.complete-profile', compact('pasien'));
    }

    public function storeCompleteProfile(Request $request)
    {
        $user = $request->user();
        $pasien = $user->pasien;

        $data = $request->validate([
            'nik' => ['required', 'digits:16', Rule::unique('pasiens', 'nik')->ignore($pasien?->id)],
            'nama' => 'required|string|max:255',
            'tgl_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_telp.required' => 'No. telepon wajib diisi.',
        ]);

        if ($pasien) {
            $pasien->update($data);
        } else {
            Pasien::create(array_merge($data, ['user_id' => $user->id]));
        }

        return redirect()->route('pasien.profile')->with('success', 'Profil pasien berhasil disimpan.');
    }

    public function edit(Pasien $pasien)
    {
        $user = Auth::user();
        $isAdmin = $user && ($user->hasRole('admin') || $user->role === 'admin');
        $isOwner = $user && $user->pasien && $user->pasien->id === $pasien->id;

        if (! $isAdmin && ! $isOwner) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data pasien ini.');
        }

        return view('pasien.edit', compact('pasien'));
    }

    public function update(UpdatePasienRequest $request, Pasien $pasien)
    {
        $pasien->update($request->validated());

        $redirect = $request->user()->hasRole('admin') || $request->user()->role === 'admin'
            ? route('pasien.index')
            : route('pasien.profile');

        return redirect($redirect)->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Pasien $pasien)
    {
        $this->authorizeAdmin();

        $pasien->delete();

        return redirect()->route('pasien.index')->with('success', 'Pasien berhasil dihapus.');
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();

        if (! $user || (! $user->hasRole('admin') && $user->role !== 'admin')) {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Administrator.');
        }
    }
}
