<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\View\View;

class LowonganController extends Controller
{
    public function index(): View
    {
        return view('public.lowongan', [
            'lowongan' => JobListing::tersedia()->with(['kategori', 'skills'])->latest()->paginate(12),
        ]);
    }

    public function show(JobListing $lowongan): View
    {
        abort_unless($lowongan->isAktif() && $lowongan->sisaKuota() > 0, 404);

        return view('public.lowongan-detail', [
            'lowongan' => $lowongan->load(['kategori', 'skills']),
        ]);
    }
}
