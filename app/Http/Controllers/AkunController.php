<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AkunController extends Controller
{
    /** Arahkan ke dashboard sesuai role. */
    public function dashboard(Request $request): RedirectResponse
    {
        return match ($request->user()->roleEnum()) {
            Role::SuperAdmin, Role::AdminSurvey, Role::AdminLapangan => redirect()->route('admin.dashboard'),
            Role::Instansi => redirect()->route('instansi.dashboard'),
            Role::PencariKerja => redirect()->route('pencari.dashboard'),
            default => abort(403, 'Akun belum memiliki peran.'),
        };
    }

    public function editPassword(): View
    {
        return view('akun.password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('success', 'Password berhasil diganti.');
    }
}
