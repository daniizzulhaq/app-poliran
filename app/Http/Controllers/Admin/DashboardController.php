<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusInstansi;
use App\Enums\StatusLowongan;
use App\Enums\StatusPenyaluran;
use App\Enums\StatusPeserta;
use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\PencariKerja;
use App\Models\Penyaluran;
use App\Models\PesertaPelatihan;
use App\Services\StatistikService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Dashboard admin. Isi menyesuaikan permission (Super Admin, Admin Survei, Admin Lapangan). */
class DashboardController extends Controller
{
    public function __invoke(Request $request, StatistikService $statistik): View
    {
        $user = $request->user();
        JobListing::tandaiKedaluwarsa();
        $data = ['user' => $user, 'kecamatanTugas' => $user->isAdminLapangan() ? $user->kecamatan()->pluck('nama')->implode(', ') : null];

        if ($user->can('lihat-laporan')) {
            $data['ringkasan'] = $statistik->ringkasanAdmin();
            $data['rekap'] = $statistik->rekapKecamatan($user);
        }

        if ($user->can('survei-instansi')) {
            $data['antrianInstansi'] = Instansi::where('status', StatusInstansi::MenungguSurvey)->oldest('diajukan_pada')->limit(5)->get();
            $data['jumlahAntrianInstansi'] = Instansi::where('status', StatusInstansi::MenungguSurvey)->count();
        }

        if ($user->can('survei-lowongan')) {
            $data['antrianLowongan'] = JobListing::with('instansi:id,nama')->where('status', StatusLowongan::MenungguSurvey)->oldest('updated_at')->limit(5)->get();
            $data['jumlahAntrianLowongan'] = JobListing::where('status', StatusLowongan::MenungguSurvey)->count();
        }

        if ($user->can('kelola-pendaftar')) {
            $data['perStatus'] = $statistik->perStatus(PencariKerja::visibleTo($user));
            $data['pendaftarBaru'] = PencariKerja::visibleTo($user)->with('kecamatan:id,nama')->latest()->limit(8)->get();
            $data['penyaluranDiproses'] = Penyaluran::where('status', StatusPenyaluran::Diproses)
                ->whereHas('pencariKerja', fn ($q) => $q->visibleTo($user))->count();
            $data['pesertaMenunggu'] = PesertaPelatihan::where('status', StatusPeserta::Mendaftar)
                ->whereHas('pencariKerja', fn ($q) => $q->visibleTo($user))->count();
        }

        return view('admin.dashboard', $data);
    }
}
