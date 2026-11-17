<?php

namespace App\Http\Controllers\PencariKerja;

use App\Http\Controllers\Controller;
use App\Http\Requests\DaftarPelatihanRequest;
use App\Models\Pelatihan;
use App\Services\PelatihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelatihanController extends Controller
{
    public function index(Request $request): View
    {
        $pencari = $request->user()->pencariKerja;

        $peserta = $pencari
            ? $pencari->pesertaPelatihan()->with(['pelatihan' => fn ($q) => $q->with('kecamatan:id,nama')])->latest()->paginate(10)
            : null;

        return view('pencari.pelatihan', compact('pencari', 'peserta'));
    }

    /** Form daftar pelatihan. Belum isi profil -> isi profil dulu, lalu kembali ke form ini. */
    public function create(Request $request, Pelatihan $pelatihan): View|RedirectResponse
    {
        $pencari = $request->user()->pencariKerja;

        if (! $pencari) {
            $request->session()->put('kembali_ke', route('pencari.pelatihan.daftar', $pelatihan));

            return redirect()->route('pencari.profil.edit')
                ->with('success', 'Lengkapi profil Anda dulu. Setelah disimpan, Anda kembali ke form pendaftaran pelatihan.');
        }

        $sudah = $pelatihan->peserta()->where('pencari_kerja_id', $pencari->id)->exists();
        $pencari->load(['kecamatan:id,nama', 'desa:id,nama']);
        $pelatihan->load(['kategori', 'kecamatan']);

        return view('pencari.daftar-pelatihan', compact('pelatihan', 'pencari', 'sudah'));
    }

    public function store(DaftarPelatihanRequest $request, Pelatihan $pelatihan, PelatihanService $service): RedirectResponse
    {
        $pencari = $request->user()->pencariKerja;
        abort_unless($pencari, 403, 'Lengkapi profil terlebih dahulu.');

        $service->daftar($pelatihan, $pencari, $request->alasan, $request->pengalaman_bidang);

        return redirect()->route('pencari.pelatihan.index')
            ->with('success', 'Pendaftaran pelatihan terkirim. Pantau status seleksi di halaman ini.');
    }
}
