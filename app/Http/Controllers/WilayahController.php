<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    /** Daftar desa di satu kecamatan (untuk dropdown bertingkat). */
    public function desa(Kecamatan $kecamatan): JsonResponse
    {
        return response()->json($kecamatan->desa()->get(['id', 'nama']));
    }
}
