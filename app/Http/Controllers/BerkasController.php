<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use App\Models\PencariKerja;
use App\Models\PesertaPelatihan;
use App\Services\FileService;
use App\Services\SertifikatService;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
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

    public function cv(PencariKerja $pencariKerja): StreamedResponse
    {
        $this->authorize('lihatCv', $pencariKerja);

        return $this->files->tampilkan($pencariKerja->cv_path, 'cv-'.Str::slug($pencariKerja->nama));
    }

    public function sertifikat(PesertaPelatihan $peserta, SertifikatService $sertifikat): Response
    {
        $this->authorize('unduhSertifikat', $peserta);

        $peserta->load(['pelatihan', 'pencariKerja']);
        $path = $sertifikat->pastikanAda($peserta);

        return $this->files->tampilkan($path, 'sertifikat-'.Str::slug($peserta->pencariKerja->nama));
    }
}
