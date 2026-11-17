<?php

namespace App\Services;

use App\Enums\StatusPelatihan;
use App\Enums\StatusPendaftar;
use App\Enums\StatusPeserta;
use App\Models\Pelatihan;
use App\Models\PencariKerja;
use App\Models\PesertaPelatihan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PelatihanService
{
    public function __construct(
        private FileService $files,
        private StatusPendaftarService $status,
        private SertifikatService $sertifikat,
    ) {}

    /** @param array<string, mixed> $data */
    public function simpan(array $data, ?UploadedFile $poster, User $oleh, ?Pelatihan $pelatihan = null): Pelatihan
    {
        $pelatihan ??= new Pelatihan(['status' => StatusPelatihan::Draft, 'dibuat_oleh' => $oleh->id]);
        $pelatihan->fill(collect($data)->except('poster')->all());

        if (! $pelatihan->exists || $pelatihan->isDirty('nama')) {
            $pelatihan->slug = $this->slugUnik($pelatihan->nama, $pelatihan->id);
        }
        if ($poster) {
            $pelatihan->poster = $this->files->simpanPublik($poster, 'poster', $pelatihan->poster);
        }
        $pelatihan->save();

        return $pelatihan;
    }

    public function ubahStatus(Pelatihan $pelatihan, StatusPelatihan $status): void
    {
        $pelatihan->update(['status' => $status]);
    }

    /** Pencari kerja mendaftar. Satu orang satu kali per pelatihan. */
    public function daftar(Pelatihan $pelatihan, PencariKerja $pencari, string $alasan, ?string $pengalaman): PesertaPelatihan
    {
        if (! $pelatihan->bisaDidaftar()) {
            throw ValidationException::withMessages(['alasan' => 'Pendaftaran pelatihan ini sudah ditutup.']);
        }
        if ($pelatihan->peserta()->where('pencari_kerja_id', $pencari->id)->exists()) {
            throw ValidationException::withMessages(['alasan' => 'Anda sudah terdaftar di pelatihan ini.']);
        }

        return $pelatihan->peserta()->create([
            'pencari_kerja_id' => $pencari->id,
            'alasan' => $alasan,
            'pengalaman_bidang' => $pengalaman,
            'status' => StatusPeserta::Mendaftar,
        ]);
    }

    /** Terima / tolak peserta. Diterima -> status pendaftar "dilatih". */
    public function seleksi(PesertaPelatihan $peserta, bool $diterima, User $oleh, ?string $catatan): void
    {
        if ($peserta->status !== StatusPeserta::Mendaftar) {
            throw ValidationException::withMessages(['status' => 'Peserta ini sudah diseleksi.']);
        }

        DB::transaction(function () use ($peserta, $diterima, $oleh, $catatan) {
            $pelatihan = Pelatihan::whereKey($peserta->pelatihan_id)->lockForUpdate()->firstOrFail();

            if ($diterima) {
                $terisi = $pelatihan->peserta()
                    ->whereIn('status', [StatusPeserta::Diterima, StatusPeserta::Lulus, StatusPeserta::TidakLulus])
                    ->count();
                if ($terisi >= $pelatihan->kuota) {
                    throw ValidationException::withMessages(['status' => 'Kuota pelatihan sudah penuh.']);
                }
            }

            $peserta->update([
                'status' => $diterima ? StatusPeserta::Diterima : StatusPeserta::Ditolak,
                'catatan' => $catatan,
                'diproses_oleh' => $oleh->id,
            ]);

            if ($diterima) {
                $this->status->ubah(PencariKerja::findOrFail($peserta->pencari_kerja_id), StatusPendaftar::Dilatih, $oleh, 'Diterima sebagai peserta pelatihan: '.$pelatihan->nama);
            }
        });
    }

    /**
     * Isi hasil pelatihan.
     * Lulus + pelatihan bersertifikat -> sertifikat PDF + status "bersertifikat".
     * Tidak lulus / pelatihan tanpa sertifikat -> status "non_bersertifikat".
     */
    public function hasil(PesertaPelatihan $peserta, bool $lulus, User $oleh, ?string $catatan): void
    {
        if ($peserta->status !== StatusPeserta::Diterima) {
            throw ValidationException::withMessages(['status' => 'Hasil hanya bisa diisi untuk peserta yang diterima.']);
        }

        DB::transaction(function () use ($peserta, $lulus, $oleh, $catatan) {
            $pelatihan = Pelatihan::findOrFail($peserta->pelatihan_id);
            $pencari = PencariKerja::findOrFail($peserta->pencari_kerja_id);
            $peserta->setRelation('pelatihan', $pelatihan)->setRelation('pencariKerja', $pencari);

            $peserta->update([
                'status' => $lulus ? StatusPeserta::Lulus : StatusPeserta::TidakLulus,
                'catatan' => $catatan,
                'diproses_oleh' => $oleh->id,
            ]);

            $dapatSertifikat = $lulus && $pelatihan->bersertifikat;
            if ($dapatSertifikat) {
                $this->sertifikat->buat($peserta);
            }

            // Pendaftar yang sudah disalurkan tidak diturunkan statusnya.
            if ($pencari->status !== StatusPendaftar::Disalurkan) {
                $this->status->ubah(
                    $pencari,
                    $dapatSertifikat ? StatusPendaftar::Bersertifikat : StatusPendaftar::NonBersertifikat,
                    $oleh,
                    ($lulus ? 'Lulus' : 'Tidak lulus').' pelatihan: '.$pelatihan->nama,
                );
            }
        });
    }

    private function slugUnik(string $nama, ?int $abaikanId): string
    {
        $dasar = Str::slug($nama) ?: 'pelatihan';
        $slug = $dasar;
        $i = 2;
        while (Pelatihan::where('slug', $slug)->when($abaikanId, fn ($q) => $q->whereKeyNot($abaikanId))->exists()) {
            $slug = $dasar.'-'.$i++;
        }

        return $slug;
    }
}
