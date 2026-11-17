<?php

namespace App\Http\Controllers\Instansi;

use App\Enums\JenisDokumen;
use App\Enums\JenisInstansi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instansi\VerifikasiInstansiRequest;
use App\Services\InstansiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $instansi = $user->instansi;

        return view('instansi.verifikasi', [
            'user' => $user,
            'instansi' => $instansi,
            'bisaUbah' => $instansi === null || $user->can('update', $instansi),
            'jenisOptions' => JenisInstansi::options(),
            'dokumenOptions' => JenisDokumen::options(),
        ]);
    }

    public function update(VerifikasiInstansiRequest $request, InstansiService $service): RedirectResponse
    {
        $service->ajukan($request->user(), $request->validated(), $request->file('file_dokumen'), $request->file('file_selfie'));

        return redirect()->route('instansi.dashboard')
            ->with('success', 'Data verifikasi terkirim. Petugas Polres akan melakukan survei. Pantau hasilnya di dashboard.');
    }
}
