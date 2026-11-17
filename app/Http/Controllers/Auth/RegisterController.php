<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\RegistrasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function pilih(): View
    {
        return view('auth.register-pilih');
    }

    public function createPencariKerja(): View
    {
        return view('auth.register', ['jenis' => 'pencari-kerja']);
    }

    public function createInstansi(): View
    {
        return view('auth.register', ['jenis' => 'instansi']);
    }

    public function storePencariKerja(RegisterRequest $request, RegistrasiService $service): RedirectResponse
    {
        $user = $service->daftar($request->validated(), Role::PencariKerja);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Akun berhasil dibuat. Silakan lengkapi profil Anda.');
    }

    public function storeInstansi(RegisterRequest $request, RegistrasiService $service): RedirectResponse
    {
        $user = $service->daftar($request->validated(), Role::Instansi);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat. Silakan lengkapi data verifikasi instansi Anda.');
    }
}
