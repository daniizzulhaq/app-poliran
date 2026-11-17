<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPeserta;
use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use App\Models\PesertaPelatihan;
use App\Services\PelatihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesertaPelatihanController extends Controller
{
    /** Daftar pelatihan + jumlah peserta yang terlihat oleh admin (per kecamatan). */
    public function index(Request $request): View
    {
        $user = $request->user();
        $terlihat = fn ($q) => $q->whereHas('pencariKerja', fn ($p) => $p->visibleTo($user));

        $pelatihan = Pelatihan::with(['kategori', 'kecamatan'])
            ->withCount([
                'peserta as peserta_count' => $terlihat,
                'peserta as menunggu_count' => fn ($q) => $terlihat($q)->where('status', StatusPeserta::Mendaftar),
            ])
            ->latest('tanggal_mulai')
            ->paginate(20);

        return view('admin.peserta.index', compact('pelatihan'));
    }

    public function pelatihan(Request $request, Pelatihan $pelatihan): View
    {
        $user = $request->user();
        $status = StatusPeserta::tryFrom((string) $request->query('status'));

        $peserta = $pelatihan->peserta()
            ->whereHas('pencariKerja', fn ($q) => $q->visibleTo($user))
            ->with(['pencariKerja' => fn ($q) => $q->with(['kecamatan:id,nama', 'desa:id,nama'])])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->oldest()
            ->paginate(30)
            ->withQueryString();

        $pelatihan->load('kategori');

        return view('admin.peserta.pelatihan', compact('pelatihan', 'peserta', 'status'));
    }

    public function seleksi(Request $request, PesertaPelatihan $peserta, PelatihanService $service): RedirectResponse
    {
        $this->authorize('kelola', $peserta);
        $data = $request->validate([
            'keputusan' => ['required', 'in:diterima,ditolak'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $service->seleksi($peserta, $data['keputusan'] === 'diterima', $request->user(), $data['catatan'] ?? null);

        return back()->with('success', 'Seleksi peserta disimpan.');
    }

    public function hasil(Request $request, PesertaPelatihan $peserta, PelatihanService $service): RedirectResponse
    {
        $this->authorize('kelola', $peserta);
        $data = $request->validate([
            'hasil' => ['required', 'in:lulus,tidak_lulus'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $service->hasil($peserta, $data['hasil'] === 'lulus', $request->user(), $data['catatan'] ?? null);

        return back()->with('success', 'Hasil pelatihan disimpan.');
    }
}
