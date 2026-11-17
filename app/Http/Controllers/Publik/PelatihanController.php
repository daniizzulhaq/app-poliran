<?php

namespace App\Http\Controllers\Publik;

use App\Enums\StatusPelatihan;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelatihanController extends Controller
{
    /** Hanya pelatihan berstatus "dibuka". */
    public function index(Request $request): View
    {
        $pelatihan = Pelatihan::dibuka()
            ->with(['kategori:id,nama', 'kecamatan:id,nama'])
            ->when($request->filled('kategori'), fn ($q) => $q->whereHas('kategori', fn ($k) => $k->where('slug', $request->kategori)))
            ->orderBy('tanggal_mulai')
            ->paginate(12)
            ->withQueryString();

        $kategori = Kategori::whereHas('pelatihan', fn ($q) => $q->dibuka())->orderBy('nama')->pluck('nama', 'slug');

        return view('public.pelatihan.index', compact('pelatihan', 'kategori'));
    }

    public function show(Pelatihan $pelatihan): View
    {
        abort_if($pelatihan->status === StatusPelatihan::Draft, 404);
        $pelatihan->load(['kategori:id,nama', 'kecamatan:id,nama']);

        return view('public.pelatihan.show', compact('pelatihan'));
    }
}
