<?php

namespace App\Services;

use App\Enums\StatusLowongan;
use App\Enums\StatusPendaftar;
use App\Enums\StatusPenyaluran;
use App\Models\JobChat;
use App\Models\JobListing;
use App\Models\PencariKerja;
use App\Models\Penyaluran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Tahapan penyaluran: Merekrut -> Melatih (Bersertifikat / Non Bersertifikat) -> Menyalurkan,
 * atau Tidak Diterima. Status pendaftar ikut naik sesuai tahap, tetapi tidak pernah diturunkan
 * (pendaftar yang sudah bersertifikat dari pelatihan tetap bersertifikat saat direkrut).
 */
class PenyaluranService
{
    public function __construct(private StatusPendaftarService $status) {}

    /** Admin memilih pendaftar dari hasil pencocokan -> tahap "merekrut". */
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

        return DB::transaction(function () use ($lowongan, $pencari, $oleh) {
            $penyaluran = Penyaluran::create([
                'job_listing_id' => $lowongan->id,
                'pencari_kerja_id' => $pencari->id,
                'status' => StatusPenyaluran::Merekrut,
                'disalurkan_oleh' => $oleh->id,
            ]);

            $this->naikkanStatusPendaftar($pencari, StatusPenyaluran::Merekrut, $lowongan, $oleh);

            if ($oleh->isSuperAdmin() && $pencari->user_id) {
                $chat = JobChat::where('user_id', $pencari->user_id)->where('job_listing_id', $lowongan->id)
                    ->lockForUpdate()->first();
                if ($chat) {
                    $pencari->loadMissing('kecamatan');
                    $chat->messages()->create([
                        'sender_id' => $oleh->id,
                        'is_admin' => true,
                        'client_id' => 'recruitment-'.$penyaluran->id,
                        'body' => 'Anda masuk tahap rekrutmen untuk lowongan “'.$lowongan->judul.'”. Proses berikutnya masuk ke antrean Admin Lapangan Kecamatan '.$pencari->kecamatan->nama.' sesuai domisili Anda. Admin Utama akan membantu koordinasi tindak lanjutnya.',
                    ]);
                    $chat->update(['last_message_at' => now()]);
                }
            }

            return $penyaluran;
        });
    }

    /**
     * Pindahkan penyaluran ke tahap berikutnya.
     * Menyalurkan -> pendaftar "disalurkan", tersalur +1, lowongan "terpenuhi" saat kuota penuh.
     * Tidak diterima -> pendaftar bisa disalurkan ke lowongan lain.
     */
    public function ubahTahap(
        Penyaluran $penyaluran,
        StatusPenyaluran $tahap,
        User $oleh,
        ?string $catatan = null,
        ?string $tanggalMulaiKerja = null,
        ?int $gaji = null,
    ): void {
        DB::transaction(function () use ($penyaluran, $tahap, $oleh, $catatan, $tanggalMulaiKerja, $gaji) {
            $penyaluran = Penyaluran::whereKey($penyaluran->id)->lockForUpdate()->firstOrFail();
            if (! in_array($tahap, $penyaluran->status->berikutnya(), true)) {
                throw ValidationException::withMessages(['status' => 'Tahap "'.$tahap->label().'" tidak bisa dipilih dari tahap "'.$penyaluran->status->label().'".']);
            }

            $lowongan = JobListing::with('instansi:id,nama')->whereKey($penyaluran->job_listing_id)->lockForUpdate()->firstOrFail();
            $pencari = PencariKerja::whereKey($penyaluran->pencari_kerja_id)->lockForUpdate()->firstOrFail();

            if ($tahap === StatusPenyaluran::Menyalurkan) {
                if ($lowongan->sisaKuota() < 1) {
                    throw ValidationException::withMessages(['status' => 'Kuota lowongan sudah penuh.']);
                }
                if ($pencari->status === StatusPendaftar::Disalurkan) {
                    throw ValidationException::withMessages(['status' => 'Pendaftar sudah disalurkan ke lowongan lain.']);
                }
            }

            $penyaluran->update([
                'status' => $tahap,
                'catatan' => filled($catatan) ? $catatan : $penyaluran->catatan,
                'diproses_oleh' => $oleh->id,
                'hasil_pada' => $tahap->selesai() ? now() : null,
            ]);

            if ($tahap === StatusPenyaluran::Menyalurkan) {
                $penyaluran->update(['tanggal_mulai_kerja' => $tanggalMulaiKerja ?? today(), 'gaji' => $gaji]);
                $lowongan->increment('jumlah_tersalur');
                if ($lowongan->jumlah_tersalur >= $lowongan->kuota) {
                    $lowongan->update(['status' => StatusLowongan::Terpenuhi, 'ditutup_pada' => now()]);
                }
            }

            if ($tahap === StatusPenyaluran::TidakDiterima) {
                $this->batalkanRekrut($pencari, $penyaluran, $lowongan, $oleh, $catatan);
            } else {
                $this->naikkanStatusPendaftar($pencari, $tahap, $lowongan, $oleh, $catatan);
            }
        });
    }

    private function naikkanStatusPendaftar(PencariKerja $pencari, StatusPenyaluran $tahap, JobListing $lowongan, User $oleh, ?string $catatan = null): void
    {
        $baru = $tahap->statusPendaftar();
        if ($baru === null || $baru->tingkat() <= $pencari->status->tingkat()) {
            return;
        }

        $this->status->ubah($pencari, $baru, $oleh, $this->keterangan($tahap, $lowongan, $catatan));
    }

    /** Pendaftar yang hanya "direkrut" untuk penyaluran ini kembali ke "terdaftar". */
    private function batalkanRekrut(PencariKerja $pencari, Penyaluran $penyaluran, JobListing $lowongan, User $oleh, ?string $catatan): void
    {
        $masihBerjalan = Penyaluran::where('pencari_kerja_id', $pencari->id)
            ->whereKeyNot($penyaluran->id)
            ->whereIn('status', StatusPenyaluran::berjalan())
            ->exists();

        if ($pencari->status === StatusPendaftar::Direkrut && ! $masihBerjalan) {
            $this->status->ubah($pencari, StatusPendaftar::Terdaftar, $oleh, $this->keterangan(StatusPenyaluran::TidakDiterima, $lowongan, $catatan));
        }
    }

    private function keterangan(StatusPenyaluran $tahap, JobListing $lowongan, ?string $catatan): string
    {
        $lowongan->loadMissing('instansi:id,nama');

        return trim('Penyaluran '.$tahap->label().': '.$lowongan->judul.' - '.$lowongan->instansi->nama.'. '.($catatan ?? ''));
    }
}
