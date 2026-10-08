<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PasienController extends Controller
{
    public function index(Request $request)
    {
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
        return view('pasien.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|string|max:16|unique:pasiens,nik',
            'nama' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
        ]);

        Pasien::create($data);

        return redirect()->route('pasien.index')->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function update(Request $request, Pasien $pasien)
    {
        $data = $request->validate([
            'nik' => ['required', 'string', 'max:16', Rule::unique('pasiens', 'nik')->ignore($pasien->id)],
            'nama' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
        ]);

        $pasien->update($data);

        return redirect()->route('pasien.index')->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Pasien $pasien)
    {
        $pasien->delete();

        return redirect()->route('pasien.index')->with('success', 'Pasien berhasil dihapus.');
    }
}
