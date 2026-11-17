<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MasterNamaRequest;
use App\Models\JobListing;
use App\Models\Kategori;
use App\Models\Pelatihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        return view('admin.master.nama', [
            'judul' => 'Kategori',
            'keterangan' => 'Kategori dipakai untuk minat pendaftar, lowongan, dan pelatihan.',
            'items' => Kategori::orderBy('nama')->paginate(50),
            'route' => 'admin.kategori',
        ]);
    }

    public function store(MasterNamaRequest $request): RedirectResponse
    {
        Kategori::create($request->validated());

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(MasterNamaRequest $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($request->validated());

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if (JobListing::where('kategori_id', $kategori->id)->exists() || Pelatihan::where('kategori_id', $kategori->id)->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena dipakai lowongan atau pelatihan.');
        }
        $kategori->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
}
