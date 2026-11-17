<?php

namespace App\Services;

use App\Enums\StatusPendaftar;
use App\Enums\StatusPeserta;
use App\Models\JobListing;
use App\Models\PencariKerja;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Pencocokan otomatis lowongan aktif <-> pendaftar. Skor 0-100:
 * - kategori lowongan ada di kategori minat (atau kategori pelatihan yang diikuti): 40
 * - skill: 40 x (skill sama / skill diminta). Lowongan tanpa syarat skill: 40
 * - pendidikan memenuhi minimal (atau tanpa syarat): 10
 * - umur masuk rentang (atau tanpa batas): 10
 * Filter: jenis kelamin bila disyaratkan; pendaftar berstatus disalurkan tidak ikut.
 */
class JobMatcher
{
    /** Status peserta yang dianggap sudah mengikuti pelatihan. */
    private const STATUS_IKUT_PELATIHAN = [StatusPeserta::Diterima, StatusPeserta::Lulus, StatusPeserta::TidakLulus];

    /**
     * @return array{skor: int, kategori: bool, skill_sama: int, skill_diminta: int, pendidikan: bool, umur: bool}|null
     *                                                                                                              null bila tidak memenuhi filter
     */
    public function skor(JobListing $lowongan, PencariKerja $pencari): ?array
    {
        if ($pencari->status === StatusPendaftar::Disalurkan) {
            return null;
        }
        if ($lowongan->jenis_kelamin && $lowongan->jenis_kelamin !== $pencari->jenis_kelamin) {
            return null;
        }

        $lowongan->loadMissing('skills');
        $pencari->loadMissing($this->relasiPencari());

        $kategoriIds = $this->kategoriPencari($pencari);
        $kategoriCocok = in_array($lowongan->kategori_id, $kategoriIds, true);

        $diminta = $lowongan->skills->pluck('id');
        $sama = $diminta->intersect($pencari->skills->pluck('id'))->count();
        $nilaiSkill = $diminta->isEmpty() ? 40 : 40 * $sama / $diminta->count();

        $pendidikanCocok = $pencari->pendidikan->memenuhi($lowongan->pendidikan_min);

        $umur = $pencari->umur();
        $umurCocok = ($lowongan->umur_min === null || $umur >= $lowongan->umur_min)
            && ($lowongan->umur_max === null || $umur <= $lowongan->umur_max);

        $skor = ($kategoriCocok ? 40 : 0) + $nilaiSkill + ($pendidikanCocok ? 10 : 0) + ($umurCocok ? 10 : 0);

        return [
            'skor' => (int) round($skor),
            'kategori' => $kategoriCocok,
            'skill_sama' => $sama,
            'skill_diminta' => $diminta->count(),
            'pendidikan' => $pendidikanCocok,
            'umur' => $umurCocok,
        ];
    }

    /**
     * Pendaftar yang cocok untuk satu lowongan, dibatasi hak akses user (per kecamatan).
     * Hanya kandidat yang kategori atau skill-nya beririsan dengan lowongan.
     *
     * @return Collection<int, array{pencari: PencariKerja, rincian: array}>
     */
    public function pendaftarCocok(JobListing $lowongan, User $user, int $batas = 50): Collection
    {
        $lowongan->loadMissing('skills');
        $skillIds = $lowongan->skills->pluck('id');

        $kandidat = PencariKerja::visibleTo($user)
            ->where('status', '!=', StatusPendaftar::Disalurkan)
            ->when($lowongan->jenis_kelamin, fn (Builder $q) => $q->where('jenis_kelamin', $lowongan->jenis_kelamin))
            ->whereDoesntHave('penyaluran', fn (Builder $q) => $q->where('job_listing_id', $lowongan->id))
            ->where(function (Builder $q) use ($lowongan, $skillIds) {
                $q->whereHas('kategori', fn (Builder $k) => $k->where('kategori.id', $lowongan->kategori_id))
                    ->orWhereHas('pesertaPelatihan', fn (Builder $p) => $p
                        ->whereIn('status', self::STATUS_IKUT_PELATIHAN)
                        ->whereHas('pelatihan', fn (Builder $pl) => $pl->where('kategori_id', $lowongan->kategori_id)));
                if ($skillIds->isNotEmpty()) {
                    $q->orWhereHas('skills', fn (Builder $s) => $s->whereIn('skill.id', $skillIds));
                }
            })
            ->with($this->relasiPencari())
            ->get();

        return $kandidat
            ->map(fn (PencariKerja $p) => ['pencari' => $p, 'rincian' => $this->skor($lowongan, $p)])
            ->filter(fn ($row) => $row['rincian'] !== null)
            ->sortByDesc(fn ($row) => $row['rincian']['skor'])
            ->take($batas)
            ->values();
    }

    /**
     * Lowongan aktif yang cocok untuk satu pendaftar.
     *
     * @return Collection<int, array{lowongan: JobListing, rincian: array}>
     */
    public function lowonganCocok(PencariKerja $pencari, int $batas = 20): Collection
    {
        if ($pencari->status === StatusPendaftar::Disalurkan) {
            return collect();
        }

        $pencari->loadMissing($this->relasiPencari());

        return JobListing::aktif()
            ->whereColumn('jumlah_tersalur', '<', 'kuota')
            ->with(['skills', 'kategori', 'instansi:id,nama'])
            ->get()
            ->map(fn (JobListing $l) => ['lowongan' => $l, 'rincian' => $this->skor($l, $pencari)])
            ->filter(fn ($row) => $row['rincian'] !== null && ($row['rincian']['kategori'] || $row['rincian']['skill_sama'] > 0))
            ->sortByDesc(fn ($row) => $row['rincian']['skor'])
            ->take($batas)
            ->values();
    }

    /** @return array<int|string, mixed> */
    private function relasiPencari(): array
    {
        return [
            'kategori:id',
            'skills:id',
            'kecamatan:id,nama',
            'desa:id,nama',
            'pesertaPelatihan' => fn ($q) => $q->whereIn('status', self::STATUS_IKUT_PELATIHAN)->with('pelatihan:id,kategori_id'),
        ];
    }

    /** @return array<int, int> */
    private function kategoriPencari(PencariKerja $pencari): array
    {
        $minat = $pencari->kategori->pluck('id');
        $pelatihan = $pencari->pesertaPelatihan
            ->filter(fn ($p) => in_array($p->status, self::STATUS_IKUT_PELATIHAN, true))
            ->map(fn ($p) => $p->pelatihan?->kategori_id);

        return $minat->merge($pelatihan)->filter()->map(fn ($id) => (int) $id)->unique()->values()->all();
    }
}
