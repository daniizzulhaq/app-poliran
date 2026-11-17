<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPendaftar;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UbahStatusRequest;
use App\Http\Requests\PencariKerjaRequest;
use App\Models\Kecamatan;
use App\Models\PencariKerja;
use App\Services\JobMatcher;
use App\Services\PencariKerjaService;
use App\Services\StatusPendaftarService;
use App\Support\OpsiForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftarController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = StatusPendaftar::tryFrom((string) $request->query('status'));

        $pendaftar = PencariKerja::visibleTo($user)
            ->with(['kecamatan:id,nama', 'desa:id,nama'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($request->filled('kecamatan_id'), fn ($q) => $q->where('kecamatan_id', $request->integer('kecamatan_id')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $cari = trim($request->q);
                $q->where(fn ($w) => $w->where('nama', 'like', "%{$cari}%")->orWhere('nik', $cari));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $kecamatan = Kecamatan::orderBy('nama')
            ->when(! $user->isSuperAdmin(), fn ($q) => $q->whereIn('id', $user->kecamatanIds()))
            ->pluck('nama', 'id');

        return view('admin.pendaftar.index', compact('pendaftar', 'status', 'kecamatan') + ['semuaKecamatan' => $user->isSuperAdmin()]);
    }

    public function create(Request $request): View
    {
        return view('admin.pendaftar.form', ['pencari' => null, ...OpsiForm::pendaftar($request->user())]);
    }

    /** Input manual oleh admin untuk warga yang tidak bisa mendaftar sendiri. */
    public function store(PencariKerjaRequest $request, PencariKerjaService $service): RedirectResponse
    {
        $pencari = $service->simpan($request->validated(), $request->file('cv'), null, null, $request->user());

        return redirect()->route('admin.pendaftar.show', $pencari)->with('success', 'Pendaftar berhasil ditambahkan.');
    }

    public function show(PencariKerja $pencariKerja, JobMatcher $matcher): View
    {
        $this->authorize('view', $pencariKerja);

        $pencariKerja->load([
            'kecamatan', 'desa', 'kategori', 'skills', 'user:id,email', 'pembuat:id,name',
            'riwayatStatus.user:id,name',
            'pesertaPelatihan.pelatihan:id,nama,slug,bersertifikat',
            'penyaluran' => fn ($q) => $q->latest()->with('jobListing:id,judul,instansi_id'),
        ]);

        return view('admin.pendaftar.show', [
            'pencari' => $pencariKerja,
            'lowonganCocok' => $matcher->lowonganCocok($pencariKerja),
            'statusOptions' => StatusPendaftar::options(),
        ]);
    }

    public function edit(Request $request, PencariKerja $pencariKerja): View
    {
        $this->authorize('update', $pencariKerja);
        $pencariKerja->load(['kategori:id', 'skills:id']);

        return view('admin.pendaftar.form', ['pencari' => $pencariKerja, ...OpsiForm::pendaftar($request->user())]);
    }

    public function update(PencariKerjaRequest $request, PencariKerja $pencariKerja, PencariKerjaService $service): RedirectResponse
    {
        $pencariKerja->loadMissing('user');
        $service->simpan($request->validated(), $request->file('cv'), $pencariKerja, $pencariKerja->user, $request->user());

        return redirect()->route('admin.pendaftar.show', $pencariKerja)->with('success', 'Data pendaftar diperbarui.');
    }

    public function ubahStatus(UbahStatusRequest $request, PencariKerja $pencariKerja, StatusPendaftarService $service): RedirectResponse
    {
        $this->authorize('ubahStatus', $pencariKerja);

        $service->ubah($pencariKerja, StatusPendaftar::from($request->status), $request->user(), $request->catatan);

        return back()->with('success', 'Status pendaftar diperbarui.');
    }
}
