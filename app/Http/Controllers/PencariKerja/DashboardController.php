<?php

namespace App\Http\Controllers\PencariKerja;

use App\Enums\StatusPendaftar;
use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $pencari = $request->user()->pencariKerja?->load([
            'kecamatan:id,nama',
            'riwayatStatus',
            'pesertaPelatihan' => fn ($q) => $q->latest()->with('pelatihan:id,nama,slug,tanggal_mulai,bersertifikat'),
        ]);

        return view('pencari.dashboard', [
            'pencari' => $pencari,
            'lowongan' => JobListing::tersedia()->with(['kategori', 'skills'])->latest()->limit(6)->get(),
            'tahapan' => StatusPendaftar::cases(),
        ]);
    }
}
