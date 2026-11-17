<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PelatihanController extends Controller
{
    public function index(): View
    {
        return view('public.home');
    }
}
