<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPenyaluran;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SalurkanRequest;
use App\Http\Requests\Admin\UbahTahapPenyaluranRequest;
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
        $status = StatusPenyaluran::tryFrom((string) $request->query('status')) ?? StatusPenyaluran::Merekrut;
        $dariAdminUtama = $request->boolean('dari_admin_utama');
        $terlihat = fn () => Penyaluran::query()->whereHas('pencariKerja', fn ($q) => $q->visibleTo($user))
            ->when($dariAdminUtama, fn ($q) => $q->dariAdminUtama());

        $jumlah = $terlihat()
            ->selectRaw('status, COUNT(*) AS jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $daftar = $terlihat()
            ->where('status', $status)
            ->with([
                'pencariKerja' => fn ($q) => $q->select('id', 'nama', 'kecamatan_id', 'no_wa')->with('kecamatan:id,nama'),
                'jobListing' => fn ($q) => $q->select('id', 'judul', 'instansi_id', 'lokasi_kerja')->with('instansi:id,nama,no_wa,penanggung_jawab'),
            ])
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.penyaluran.index', compact('daftar', 'status', 'jumlah', 'dariAdminUtama'));
    }

    public function store(SalurkanRequest $request, JobListing $lowongan, PenyaluranService $service): RedirectResponse
    {
        $pencari = PencariKerja::findOrFail($request->integer('pencari_kerja_id'));
        $this->authorize('salurkan', $lowongan);
        $this->authorize('salurkan', $pencari);

        $service->salurkan($lowongan, $pencari, $request->user());

        if ($request->user()->isSuperAdmin()) {
            $pencari->loadMissing('kecamatan');
            $admins = $pencari->kecamatan->adminLapanganAktif()->pluck('users.name')->implode(', ');
            $pesan = $pencari->nama.' direkrut untuk "'.$lowongan->judul.'" dan masuk antrean Admin Lapangan Kecamatan '.$pencari->kecamatan->nama.'.';
            $pesan .= $admins ? ' Ditindaklanjuti oleh: '.$admins.'.' : ' Belum ada Admin Lapangan aktif di kecamatan ini. Tetapkan petugas melalui menu Akun Admin agar dapat ditindaklanjuti.';

            return back()->with('success', $pesan);
        }

        return back()->with('success', $pencari->nama.' direkrut untuk "'.$lowongan->judul.'" (tahap Merekrut). Hubungi instansi dan pendaftar, lalu lanjutkan tahapnya di menu Penyaluran.');
    }

    public function tahap(UbahTahapPenyaluranRequest $request, Penyaluran $penyaluran, PenyaluranService $service): RedirectResponse
    {
        $tahap = StatusPenyaluran::from($request->status);
        $service->ubahTahap($penyaluran, $tahap, $request->user(), $request->catatan, $request->tanggal_mulai_kerja, $request->integer('gaji') ?: null);

        return back()->with('success', 'Penyaluran dipindah ke tahap '.$tahap->label().'.');
    }
}
