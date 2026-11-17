<?php

namespace App\Services;

use App\Enums\StatusLowongan;
use App\Enums\StatusPendaftar;
use App\Enums\StatusPenyaluran;
use App\Models\JobListing;
use App\Models\PencariKerja;
use App\Models\Penyaluran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenyaluranService
{
    public function __construct(private StatusPendaftarService $status) {}

    /** Admin memilih pendaftar dari hasil pencocokan -> status penyaluran "diproses". */
    public function salurkan(JobListing $lowongan, PencariKerja $pencari, User $oleh): Penyaluran
    {
        if (! $lowongan->isAktif() || $lowongan->sisaKuota() < 1) {
            throw ValidationException::withMessages(['lowongan' => 'Lowongan sudah tidak aktif atau kuota penuh.']);
        }
        if ($pencari->status === StatusPendaftar::Disalurkan) {
            throw ValidationException::withMessages(['pencari_kerja_id' => 'Pendaftar sudah disalurkan.']);
        }
        if (Penyaluran::where('job_listing_id', $lowongan->id)->where('pencari_kerja_id', $pencari->id)->exists()) {
            throw ValidationException::withMessages(['pencari_kerja_id' => 'Pendaftar sudah pernah disalurkan ke lowongan ini.']);
        }

        return Penyaluran::create([
            'job_listing_id' => $lowongan->id,
            'pencari_kerja_id' => $pencari->id,
            'status' => StatusPenyaluran::Diproses,
            'disalurkan_oleh' => $oleh->id,
        ]);
    }

    /**
     * Catat hasil penyaluran.
     * Diterima -> pendaftar "disalurkan", tersalur +1, lowongan "terpenuhi" saat kuota penuh.
     * Tidak diterima -> pendaftar bisa disalurkan ke lowongan lain.
     */
    public function catatHasil(Penyaluran $penyaluran, StatusPenyaluran $hasil, User $oleh, ?string $catatan = null): void
    {
        if ($penyaluran->status !== StatusPenyaluran::Diproses || $hasil === StatusPenyaluran::Diproses) {
            throw ValidationException::withMessages(['status' => 'Hasil penyaluran sudah dicatat.']);
        }

        DB::transaction(function () use ($penyaluran, $hasil, $oleh, $catatan) {
            $lowongan = JobListing::whereKey($penyaluran->job_listing_id)->lockForUpdate()->firstOrFail();

            if ($hasil === StatusPenyaluran::Diterima && $lowongan->sisaKuota() < 1) {
                throw ValidationException::withMessages(['status' => 'Kuota lowongan sudah penuh.']);
            }

            $penyaluran->update([
                'status' => $hasil,
                'catatan' => $catatan,
                'diproses_oleh' => $oleh->id,
                'hasil_pada' => now(),
            ]);

            if ($hasil !== StatusPenyaluran::Diterima) {
                return;
            }

            $lowongan->increment('jumlah_tersalur');
            if ($lowongan->jumlah_tersalur >= $lowongan->kuota) {
                $lowongan->update(['status' => StatusLowongan::Terpenuhi, 'ditutup_pada' => now()]);
            }

            $pencari = PencariKerja::whereKey($penyaluran->pencari_kerja_id)->lockForUpdate()->firstOrFail();
            $lowongan->loadMissing('instansi:id,nama');
            $this->status->ubah(
                $pencari,
                StatusPendaftar::Disalurkan,
                $oleh,
                trim('Diterima kerja: '.$lowongan->judul.' - '.$lowongan->instansi->nama.'. '.($catatan ?? '')),
            );
        });
    }
}
