<?php

namespace App\Services;

use App\Models\PesertaPelatihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/** Membuat sertifikat PDF peserta lulus (disimpan private). */
class SertifikatService
{
    public function buat(PesertaPelatihan $peserta): string
    {
        $peserta->loadMissing(['pelatihan.kecamatan', 'pencariKerja']);

        if (! $peserta->nomor_sertifikat) {
            $peserta->nomor_sertifikat = sprintf(
                'POLIRAN/%s/%04d/%05d',
                $peserta->pelatihan->tanggal_mulai->format('Y'),
                $peserta->pelatihan_id,
                $peserta->id,
            );
        }

        $pdf = Pdf::loadView('pdf.sertifikat', ['peserta' => $peserta])->setPaper('a4', 'landscape');
        $path = 'sertifikat/'.$peserta->id.'.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        $peserta->sertifikat_path = $path;
        $peserta->save();

        return $path;
    }

    /** Pastikan berkas sertifikat ada (dibuat ulang bila hilang). */
    public function pastikanAda(PesertaPelatihan $peserta): string
    {
        abort_unless($peserta->nomor_sertifikat, 404, 'Sertifikat belum tersedia.');

        if ($peserta->sertifikat_path && Storage::disk('local')->exists($peserta->sertifikat_path)) {
            return $peserta->sertifikat_path;
        }

        return $this->buat($peserta);
    }
}
