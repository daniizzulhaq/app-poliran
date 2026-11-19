<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusLowongan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SurveiRequest;
use App\Models\JobListing;
use App\Services\JobListingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveiLowonganController extends Controller
{
    /** Antrian lowongan yang menunggu survei. Detail & form survei ada di halaman detail lowongan. */
    public function index(Request $request): View
    {
        $lowongan = JobListing::with(['instansi:id,nama,status', 'kategori'])
            ->where('status', StatusLowongan::MenungguSurvey)
            ->when($request->filled('q'), fn ($q) => $q->where('judul', 'like', '%'.$request->q.'%'))
            ->orderBy('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.survei-lowongan.index', compact('lowongan'));
    }

    public function putusan(SurveiRequest $request, JobListing $lowongan, JobListingService $service): RedirectResponse
    {
        $this->authorize('survei', $lowongan);

        $service->putuskan(
            $lowongan, $request->user(), $request->diterima(), $request->catatan_survei, $request->alasan_tolak, $request->boolean('legalitas_diperiksa'),
        );

        return redirect()->route('admin.survei-lowongan.index')
            ->with('success', $request->diterima() ? 'Lowongan disetujui dan sekarang aktif.' : 'Lowongan ditolak.');
    }
}
