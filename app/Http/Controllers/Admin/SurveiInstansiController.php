<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusInstansi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SurveiRequest;
use App\Models\Instansi;
use App\Services\InstansiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveiInstansiController extends Controller
{
    public function index(Request $request): View
    {
        $status = StatusInstansi::tryFrom((string) $request->query('status')) ?? StatusInstansi::MenungguSurvey;

        $instansi = Instansi::with('user')
            ->where('status', $status)
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%'.$request->q.'%'))
            ->orderBy($status === StatusInstansi::MenungguSurvey ? 'diajukan_pada' : 'disurvei_pada', $status === StatusInstansi::MenungguSurvey ? 'asc' : 'desc')
            ->paginate(20)
            ->withQueryString();

        $jumlah = Instansi::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.survei-instansi.index', compact('instansi', 'status', 'jumlah'));
    }

    public function show(Instansi $instansi): View
    {
        $this->authorize('view', $instansi);
        $instansi->load(['user', 'surveyor'])->loadCount('jobListings');

        return view('admin.survei-instansi.show', compact('instansi'));
    }

    public function putusan(SurveiRequest $request, Instansi $instansi, InstansiService $service): RedirectResponse
    {
        $this->authorize('survei', $instansi);

        $service->putuskan($instansi, $request->user(), $request->diterima(), $request->catatan_survei, $request->alasan_tolak);

        return redirect()->route('admin.survei-instansi.index')
            ->with('success', $request->diterima() ? 'Instansi diverifikasi.' : 'Instansi ditolak. Instansi bisa memperbaiki dan mengirim ulang.');
    }
}
