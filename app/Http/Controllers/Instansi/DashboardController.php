<?php

namespace App\Http\Controllers\Instansi;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $instansi = $request->user()->instansi;
        $data = ['instansi' => $instansi];

        if ($instansi) {
            JobListing::tandaiKedaluwarsa();
            $data['ringkasan'] = [
                'per_status' => $instansi->jobListings()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
                'tersalur' => (int) $instansi->jobListings()->sum('jumlah_tersalur'),
                'kuota' => (int) $instansi->jobListings()->sum('kuota'),
            ];
            $data['terbaru'] = $instansi->jobListings()->latest()->limit(5)->get();
        }

        return view('instansi.dashboard', $data);
    }
}
