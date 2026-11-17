<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HalamanController extends Controller
{
    public function tentang(): View
    {
        return view('public.home');
    }

    public function kontak(): View
    {
        return view('public.home');
    }
}
