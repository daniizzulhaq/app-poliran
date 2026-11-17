<?php

namespace App\Http\Controllers\PencariKerja;

use App\Http\Controllers\Controller;
use App\Http\Requests\PencariKerjaRequest;
use App\Services\PencariKerjaService;
use App\Support\OpsiForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $pencari = $user->pencariKerja?->load(['kategori:id', 'skills:id']);

        return view('pencari.profil', ['user' => $user, 'pencari' => $pencari, ...OpsiForm::pendaftar()]);
    }

    public function update(PencariKerjaRequest $request, PencariKerjaService $service): RedirectResponse
    {
        $user = $request->user();
        $service->simpan($request->validated(), $request->file('cv'), $user->pencariKerja, $user, $user);

        $kembali = $request->session()->pull('kembali_ke');

        return redirect($kembali ?: route('pencari.dashboard'))->with('success', 'Profil tersimpan.');
    }
}
