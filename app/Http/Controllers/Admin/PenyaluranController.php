<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPenyaluran;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HasilPenyaluranRequest;
use App\Http\Requests\Admin\SalurkanRequest;
use App\Models\JobListing;
use App\Models\PencariKerja;
use App\Models\Penyaluran;
use App\Services\PenyaluranService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenyaluranController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $status = StatusPenyaluran::tryFrom((string) $request->query('status')) ?? StatusPenyaluran::Diproses;

        $daftar = Penyaluran::query()
            ->whereHas('pencariKerja', fn ($q) => $q->visibleTo($user))
            ->where('status', $status)
            ->with([
                'pencariKerja' => fn ($q) => $q->select('id', 'nama', 'kecamatan_id', 'no_wa')->with('kecamatan:id,nama'),
                'jobListing' => fn ($q) => $q->select('id', 'judul', 'instansi_id')->with('instansi:id,nama,no_wa'),
            ])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.penyaluran.index', compact('daftar', 'status'));
    }

    public function store(SalurkanRequest $request, JobListing $lowongan, PenyaluranService $service): RedirectResponse
    {
        $pencari = PencariKerja::findOrFail($request->integer('pencari_kerja_id'));
        $this->authorize('salurkan', $lowongan);
        $this->authorize('salurkan', $pencari);

        $service->salurkan($lowongan, $pencari, $request->user());

        return back()->with('success', $pencari->nama.' disalurkan ke "'.$lowongan->judul.'" (diproses). Hubungi instansi dan pendaftar, lalu catat hasilnya.');
    }

    public function hasil(HasilPenyaluranRequest $request, Penyaluran $penyaluran, PenyaluranService $service): RedirectResponse
    {
        $service->catatHasil($penyaluran, StatusPenyaluran::from($request->status), $request->user(), $request->catatan);

        return back()->with('success', 'Hasil penyaluran disimpan.');
    }
}
