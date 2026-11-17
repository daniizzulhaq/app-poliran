<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KontenRequest;
use App\Services\FileService;
use App\Services\PengaturanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KontenController extends Controller
{
    public function edit(PengaturanService $pengaturan): View
    {
        $konten = $pengaturan->semua();
        $fotoUrl = $konten['sambutan_foto'] ? Storage::disk('public')->url($konten['sambutan_foto']) : null;

        return view('admin.konten.edit', compact('konten', 'fotoUrl'));
    }

    public function update(KontenRequest $request, PengaturanService $pengaturan, FileService $files): RedirectResponse
    {
        $data = collect($request->validated())->except('sambutan_foto')->all();

        if ($request->hasFile('sambutan_foto')) {
            $data['sambutan_foto'] = $files->simpanPublik($request->file('sambutan_foto'), 'konten', $pengaturan->get('sambutan_foto'));
        }

        $pengaturan->simpan($data);

        return back()->with('success', 'Konten halaman awal disimpan.');
    }
}
