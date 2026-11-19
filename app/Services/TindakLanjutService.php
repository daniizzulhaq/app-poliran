<?php

namespace App\Services;

use App\Enums\StatusPendaftar;
use App\Enums\StatusTindakLanjut;
use App\Models\PencariKerja;
use App\Models\Penyaluran;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Tindak lanjut pekerja tersalur pada bulan ke-1, 3, 6 setelah mulai kerja.
 * Berhenti bekerja -> pendaftar kembali ke daftar pencari kerja (bisa dicocokkan & disalurkan lagi).
 */
class TindakLanjutService
{
    public function __construct(private StatusPendaftarService $status) {}

    public function catat(Penyaluran $penyaluran, StatusTindakLanjut $hasil, User $oleh, ?string $catatan = null): TindakLanjut
    {
        return DB::transaction(function () use ($penyaluran, $hasil, $oleh, $catatan) {
            $penyaluran = Penyaluran::with(['tindakLanjut', 'jobListing.instansi:id,nama'])
                ->whereKey($penyaluran->id)->lockForUpdate()->firstOrFail();

            $bulan = $penyaluran->bulanTindakLanjutBerikutnya();
            if ($bulan === null) {
                throw ValidationException::withMessages(['status' => 'Tindak lanjut untuk pekerja ini sudah selesai.']);
            }
            if ($penyaluran->jatuhTempo($bulan)->isFuture()) {
                throw ValidationException::withMessages([
                    'status' => 'Tindak lanjut bulan ke-'.$bulan.' baru bisa dicatat mulai '.$penyaluran->jatuhTempo($bulan)->format('d/m/Y').'.',
                ]);
            }

            $tindakLanjut = $penyaluran->tindakLanjut()->create([
                'bulan_ke' => $bulan,
                'status' => $hasil,
                'catatan' => $catatan,
                'dicatat_oleh' => $oleh->id,
            ]);

            if ($hasil === StatusTindakLanjut::Berhenti) {
                $this->kembalikanKePencariKerja($penyaluran, $oleh, $bulan, $catatan);
            }

            return $tindakLanjut;
        });
    }

    /** Status kembali seperti sebelum disalurkan (pelatihan/sertifikat tetap); yang hanya "direkrut" kembali "terdaftar". */
    private function kembalikanKePencariKerja(Penyaluran $penyaluran, User $oleh, int $bulan, ?string $catatan): void
    {
        $pencari = PencariKerja::whereKey($penyaluran->pencari_kerja_id)->lockForUpdate()->firstOrFail();
        if ($pencari->status !== StatusPendaftar::Disalurkan) {
            return;
        }

        $sebelum = $pencari->riwayatStatus()->where('status_baru', StatusPendaftar::Disalurkan)->first()?->status_lama;
        $kembali = $sebelum && $sebelum->tingkat() > StatusPendaftar::Direkrut->tingkat() && $sebelum !== StatusPendaftar::Disalurkan
            ? $sebelum
            : StatusPendaftar::Terdaftar;

        $lowongan = $penyaluran->jobListing;
        $this->status->ubah($pencari, $kembali, $oleh, trim(
            'Berhenti bekerja dari '.$lowongan->judul.' - '.$lowongan->instansi->nama.' (tindak lanjut bulan ke-'.$bulan.'). '.($catatan ?? '')
        ));
    }
}
