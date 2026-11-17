<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Penyimpanan berkas.
 * - Sensitif (KTP/NIB, selfie, CV, sertifikat): disk "local" (private), diunduh lewat BerkasController + Policy.
 * - Publik (poster, foto landing): disk "public".
 */
class FileService
{
    public function simpanPrivate(UploadedFile $file, string $folder, ?string $lama = null): string
    {
        $path = $file->store($folder, 'local');
        $this->hapusPrivate($lama);

        return $path;
    }

    public function simpanPublik(UploadedFile $file, string $folder, ?string $lama = null): string
    {
        $path = $file->store($folder, 'public');
        $this->hapusPublik($lama);

        return $path;
    }

    public function hapusPrivate(?string $path): void
    {
        if ($path) {
            Storage::disk('local')->delete($path);
        }
    }

    public function hapusPublik(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /** Tampilkan berkas private di browser (inline). */
    public function tampilkan(?string $path, string $namaUnduh): StreamedResponse
    {
        abort_unless($path && Storage::disk('local')->exists($path), 404, 'Berkas tidak ditemukan.');

        $ext = pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk('local')->response($path, $namaUnduh.'.'.$ext, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
