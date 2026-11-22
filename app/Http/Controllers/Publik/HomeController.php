<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use App\Models\Pelatihan;
use App\Models\Testimoni;
use App\Services\PengaturanService;
use App\Services\StatistikService;
use Illuminate\View\View;

/** Halaman awal dengan lowongan yang sudah disetujui petugas. */
class HomeController extends Controller
{
    public function __invoke(PengaturanService $pengaturan, StatistikService $statistik): View
    {
        return view('public.home', [
            'konten' => $pengaturan->semua(),
            'lowongan' => JobListing::tersedia()->with(['kategori', 'skills'])->latest()->limit(6)->get(),
            'statistik' => $statistik->publik(),
            'pelatihan' => Pelatihan::dibuka()->with(['kategori:id,nama', 'kecamatan:id,nama'])->orderBy('tanggal_mulai')->limit(6)->get(),
            'testimoni' => Testimoni::where('aktif', true)->orderBy('urutan')->limit(6)->get(),
        ]);
    }
}
