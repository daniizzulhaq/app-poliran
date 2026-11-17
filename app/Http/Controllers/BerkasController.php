<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Services\FileService;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Akses berkas private. Setiap berkas dicek lewat Policy. */
class BerkasController extends Controller
{
    public function __construct(private FileService $files) {}

    public function instansi(Instansi $instansi, string $jenis): StreamedResponse
    {
        $this->authorize('view', $instansi);

        $path = match ($jenis) {
            'dokumen' => $instansi->file_dokumen,
            'selfie' => $instansi->file_selfie,
            default => abort(404),
        };

        return $this->files->tampilkan($path, $jenis.'-'.Str::slug($instansi->nama));
    }
}
