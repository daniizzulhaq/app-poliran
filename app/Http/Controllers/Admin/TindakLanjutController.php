<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPenyaluran;
use App\Enums\StatusTindakLanjut;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TindakLanjutRequest;
use App\Models\Penyaluran;
use App\Services\TindakLanjutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Tindak lanjut pekerja tersalur: masih bekerja atau tidak pada bulan ke-1, 3, 6. */
class TindakLanjutController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $tab = $request->query('tab') === 'semua' ? 'semua' : 'jatuh-tempo';
        $tersalur = fn () => Penyaluran::query()
            ->where('status', StatusPenyaluran::Menyalurkan)
            ->whereHas('pencariKerja', fn ($q) => $q->visibleTo($user));

        $jumlahJatuhTempo = $tersalur()->tindakLanjutJatuhTempo()->count();

        $daftar = $tersalur()
            ->when(
                $tab === 'jatuh-tempo',
                fn ($q) => $q->tindakLanjutJatuhTempo()->orderBy('tanggal_mulai_kerja'),
                fn ($q) => $q->latest('tanggal_mulai_kerja'),
            )
            ->with([
                'pencariKerja' => fn ($q) => $q->select('id', 'nama', 'kecamatan_id', 'no_wa', 'status')->with('kecamatan:id,nama'),
                'jobListing' => fn ($q) => $q->select('id', 'judul', 'instansi_id')->with('instansi:id,nama,no_wa'),
                'tindakLanjut',
            ])
            ->paginate(20)
            ->withQueryString();

        return view('admin.tindak-lanjut.index', compact('daftar', 'tab', 'jumlahJatuhTempo'));
    }

    public function store(TindakLanjutRequest $request, Penyaluran $penyaluran, TindakLanjutService $service): RedirectResponse
    {
        $hasil = StatusTindakLanjut::from($request->status);
        $tindakLanjut = $service->catat($penyaluran, $hasil, $request->user(), $request->catatan);

        $pesan = 'Tindak lanjut bulan ke-'.$tindakLanjut->bulan_ke.' dicatat: '.$hasil->label().'.';
        if ($hasil === StatusTindakLanjut::Berhenti) {
            $pesan .= ' Pendaftar dikembalikan ke daftar pencari kerja agar bisa disalurkan lagi.';
        }

        return back()->with('success', $pesan);
    }
}
