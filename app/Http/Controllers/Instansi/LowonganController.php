<?php

namespace App\Http\Controllers\Instansi;

use App\Enums\JenisKelamin;
use App\Enums\Pendidikan;
use App\Enums\StatusPenyaluran;
use App\Enums\TipeKerja;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instansi\LowonganRequest;
use App\Models\JobListing;
use App\Models\Kategori;
use App\Models\Skill;
use App\Services\JobListingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LowonganController extends Controller
{
    public function index(Request $request): View
    {
        JobListing::tandaiKedaluwarsa();
        $instansi = $request->user()->instansi;

        $lowongan = JobListing::visibleTo($request->user())
            ->with('kategori')
            ->latest()
            ->paginate(15);

        return view('instansi.lowongan.index', compact('lowongan', 'instansi'));
    }

    public function create(): View
    {
        $this->authorize('create', JobListing::class);

        return view('instansi.lowongan.form', ['lowongan' => new JobListing(['kuota' => 1]), ...$this->opsi()]);
    }

    public function store(LowonganRequest $request, JobListingService $service): RedirectResponse
    {
        $lowongan = $service->simpan($request->user()->instansi, $request->validated());

        return redirect()->route('instansi.lowongan.show', $lowongan)
            ->with('success', 'Lowongan terkirim dan menunggu survei petugas Polres.');
    }

    public function show(JobListing $lowongan): View
    {
        $this->authorize('view', $lowongan);
        $lowongan->load(['kategori', 'skills']);

        $tersalur = $lowongan->penyaluran()
            ->where('status', StatusPenyaluran::Diterima)
            ->with('pencariKerja:id,nama,jenis_kelamin,no_wa')
            ->latest('hasil_pada')
            ->get();

        return view('instansi.lowongan.show', compact('lowongan', 'tersalur'));
    }

    public function edit(JobListing $lowongan): View
    {
        $this->authorize('update', $lowongan);
        $lowongan->load('skills');

        return view('instansi.lowongan.form', compact('lowongan') + $this->opsi());
    }

    public function update(LowonganRequest $request, JobListing $lowongan, JobListingService $service): RedirectResponse
    {
        $service->simpan($request->user()->instansi, $request->validated(), $lowongan);

        return redirect()->route('instansi.lowongan.show', $lowongan)
            ->with('success', 'Lowongan diperbarui dan dikirim ulang untuk disurvei.');
    }

    public function tutup(Request $request, JobListing $lowongan, JobListingService $service): RedirectResponse
    {
        $this->authorize('tutup', $lowongan);
        $service->tutup($lowongan, $request->user());

        return back()->with('success', 'Lowongan ditutup.');
    }

    /** @return array<string, mixed> */
    private function opsi(): array
    {
        return [
            'kategori' => Kategori::orderBy('nama')->pluck('nama', 'id')->all(),
            'skills' => Skill::orderBy('nama')->pluck('nama', 'id')->all(),
            'pendidikan' => Pendidikan::options(),
            'jenisKelamin' => JenisKelamin::options(),
            'tipeKerja' => TipeKerja::options(),
        ];
    }
}
