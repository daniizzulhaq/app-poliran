<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Services\PengaturanService;
use App\Services\StatistikService;
use Illuminate\View\View;

class HalamanController extends Controller
{
    public function tentang(PengaturanService $pengaturan, StatistikService $statistik): View
    {
        return view('public.tentang', ['konten' => $pengaturan->semua(), 'statistik' => $statistik->publik()]);
    }

    public function kontak(PengaturanService $pengaturan): View
    {
        return view('public.kontak', ['konten' => $pengaturan->semua()]);
    }
}
