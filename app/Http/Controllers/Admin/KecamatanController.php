<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DesaRequest;
use App\Http\Requests\Admin\KecamatanRequest;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\PencariKerja;
use App\Models\Pelatihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KecamatanController extends Controller
{
    public function index(): View
    {
        $kecamatan = Kecamatan::withCount('desa')->orderBy('nama')->paginate(30);

        return view('admin.master.kecamatan', compact('kecamatan'));
    }

    public function store(KecamatanRequest $request): RedirectResponse
    {
        Kecamatan::create($request->validated());

        return back()->with('success', 'Kecamatan ditambahkan.');
    }

    public function show(Kecamatan $kecamatan): View
    {
        $desa = $kecamatan->desa()->paginate(50);

        return view('admin.master.desa', compact('kecamatan', 'desa'));
    }

    public function update(KecamatanRequest $request, Kecamatan $kecamatan): RedirectResponse
    {
        $kecamatan->update($request->validated());

        return back()->with('success', 'Kecamatan diperbarui.');
    }

    public function destroy(Kecamatan $kecamatan): RedirectResponse
    {
        $dipakai = PencariKerja::where('kecamatan_id', $kecamatan->id)->exists()
            || Pelatihan::where('kecamatan_id', $kecamatan->id)->exists();
        if ($dipakai) {
            return back()->with('error', 'Kecamatan tidak bisa dihapus karena sudah dipakai data pendaftar atau pelatihan.');
        }
        $kecamatan->delete();

        return redirect()->route('admin.kecamatan.index')->with('success', 'Kecamatan dihapus.');
    }

    public function storeDesa(DesaRequest $request, Kecamatan $kecamatan): RedirectResponse
    {
        $kecamatan->desa()->create($request->validated());

        return back()->with('success', 'Desa ditambahkan.');
    }

    public function updateDesa(DesaRequest $request, Kecamatan $kecamatan, Desa $desa): RedirectResponse
    {
        abort_unless($desa->kecamatan_id === $kecamatan->id, 404);
        $desa->update($request->validated());

        return back()->with('success', 'Desa diperbarui.');
    }

    public function destroyDesa(Kecamatan $kecamatan, Desa $desa): RedirectResponse
    {
        abort_unless($desa->kecamatan_id === $kecamatan->id, 404);
        if (PencariKerja::where('desa_id', $desa->id)->exists()) {
            return back()->with('error', 'Desa tidak bisa dihapus karena sudah dipakai data pendaftar.');
        }
        $desa->delete();

        return back()->with('success', 'Desa dihapus.');
    }
}
