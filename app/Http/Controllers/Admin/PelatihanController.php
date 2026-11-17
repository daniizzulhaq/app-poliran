<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPelatihan;
use App\Enums\StatusPeserta;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PelatihanRequest;
use App\Models\Kategori;
use App\Models\Kecamatan;
use App\Models\Pelatihan;
use App\Services\PelatihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PelatihanController extends Controller
{
    public function index(Request $request): View
    {
        $status = StatusPelatihan::tryFrom((string) $request->query('status'));

        $pelatihan = Pelatihan::with(['kategori', 'kecamatan'])
            ->withCount([
                'peserta',
                'peserta as peserta_diterima_count' => fn ($q) => $q->whereIn('status', [StatusPeserta::Diterima, StatusPeserta::Lulus, StatusPeserta::TidakLulus]),
            ])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%'.$request->q.'%'))
            ->latest('tanggal_mulai')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pelatihan.index', compact('pelatihan', 'status'));
    }

    public function create(): View
    {
        return view('admin.pelatihan.form', [
            'pelatihan' => new Pelatihan(['penyelenggara' => 'Polres Timor Tengah Selatan', 'bersertifikat' => true, 'kuota' => 20]),
            ...$this->opsi(),
        ]);
    }

    public function store(PelatihanRequest $request, PelatihanService $service): RedirectResponse
    {
        $pelatihan = $service->simpan($request->validated(), $request->file('poster'), $request->user());

        return redirect()->route('admin.pelatihan.show', $pelatihan)
            ->with('success', 'Pelatihan dibuat dengan status Draft. Ubah status ke "Dibuka" agar tampil di halaman publik.');
    }

    public function show(Pelatihan $pelatihan): View
    {
        $pelatihan->load(['kategori', 'kecamatan']);
        $jumlah = $pelatihan->peserta()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.pelatihan.show', compact('pelatihan', 'jumlah'));
    }

    public function edit(Pelatihan $pelatihan): View
    {
        return view('admin.pelatihan.form', compact('pelatihan') + $this->opsi());
    }

    public function update(PelatihanRequest $request, Pelatihan $pelatihan, PelatihanService $service): RedirectResponse
    {
        $service->simpan($request->validated(), $request->file('poster'), $request->user(), $pelatihan);

        return redirect()->route('admin.pelatihan.show', $pelatihan)->with('success', 'Pelatihan diperbarui.');
    }

    public function ubahStatus(Request $request, Pelatihan $pelatihan, PelatihanService $service): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::enum(StatusPelatihan::class)]]);
        $service->ubahStatus($pelatihan, StatusPelatihan::from($data['status']));

        return back()->with('success', 'Status pelatihan diubah.');
    }

    /** @return array<string, mixed> */
    private function opsi(): array
    {
        return [
            'kategori' => Kategori::orderBy('nama')->pluck('nama', 'id')->all(),
            'kecamatan' => Kecamatan::orderBy('nama')->pluck('nama', 'id')->all(),
        ];
    }
}
