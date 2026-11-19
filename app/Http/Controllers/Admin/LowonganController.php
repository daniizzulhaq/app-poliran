<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusInstansi;
use App\Enums\StatusLowongan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LowonganPetugasRequest;
use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\Kategori;
use App\Services\JobListingService;
use App\Services\JobMatcher;
use App\Support\OpsiForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LowonganController extends Controller
{
    public function index(Request $request): View
    {
        JobListing::tandaiKedaluwarsa();

        $status = StatusLowongan::tryFrom((string) $request->query('status'));

        $lowongan = JobListing::visibleTo($request->user())
            ->with(['instansi:id,nama', 'kategori'])
            ->when($status, fn ($q) => $q->where('status', $status), fn ($q) => $q->where('status', StatusLowongan::Aktif))
            ->when($request->filled('kategori_id'), fn ($q) => $q->where('kategori_id', $request->integer('kategori_id')))
            ->when($request->filled('q'), fn ($q) => $q->where('judul', 'like', '%'.$request->q.'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.lowongan.index', [
            'lowongan' => $lowongan,
            'status' => $status ?? StatusLowongan::Aktif,
            'kategori' => Kategori::orderBy('nama')->pluck('nama', 'id')->all(),
        ]);
    }

    /** Petugas survei menginput lowongan atas nama instansi terverifikasi (jemput bola). */
    public function create(): View
    {
        return view('instansi.lowongan.form', [
            'lowongan' => new JobListing(['kuota' => 1]),
            'petugas' => true,
            'instansiOptions' => Instansi::where('status', StatusInstansi::Terverifikasi)->orderBy('nama')->pluck('nama', 'id')->all(),
            ...OpsiForm::lowongan(),
        ]);
    }

    public function store(LowonganPetugasRequest $request, JobListingService $service): RedirectResponse
    {
        $instansi = Instansi::findOrFail($request->integer('instansi_id'));
        $lowongan = $service->buatOlehPetugas($instansi, $request->validated(), $request->user());

        return redirect()->route('admin.lowongan.show', $lowongan)->with('success', 'Lowongan disimpan dan langsung aktif. Cocokkan dengan pendaftar di tab Pendaftar Cocok.');
    }

    public function show(Request $request, JobListing $lowongan, JobMatcher $matcher): View
    {
        $this->authorize('view', $lowongan);
        JobListing::tandaiKedaluwarsa();
        $lowongan->refresh()->load(['instansi', 'kategori', 'skills', 'surveyor']);

        $user = $request->user();
        $cocok = $user->can('kelola-penyaluran') && $lowongan->isAktif()
            ? $matcher->pendaftarCocok($lowongan, $user)
            : collect();

        $penyaluran = $lowongan->penyaluran()
            ->with(['pencariKerja' => fn ($q) => $q->select('id', 'nama', 'kecamatan_id', 'no_wa')->with('kecamatan:id,nama')])
            ->whereHas('pencariKerja', fn ($q) => $q->visibleTo($user))
            ->latest()
            ->get();

        return view('admin.lowongan.show', compact('lowongan', 'cocok', 'penyaluran'));
    }

    public function tutup(Request $request, JobListing $lowongan, JobListingService $service): RedirectResponse
    {
        $this->authorize('tutup', $lowongan);
        $service->tutup($lowongan, $request->user());

        return back()->with('success', 'Lowongan ditutup.');
    }
}
