<?php

namespace App\Services;

use App\Enums\StatusInstansi;
use App\Enums\StatusLowongan;
use App\Enums\StatusPelatihan;
use App\Enums\StatusPendaftar;
use App\Enums\StatusPenyaluran;
use App\Enums\StatusTindakLanjut;
use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\Kecamatan;
use App\Models\Pelatihan;
use App\Models\PencariKerja;
use App\Models\Penyaluran;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class StatistikService
{
    /**
     * Statistik publik (halaman awal). Di-cache 10 menit.
     *
     * @return array<string, int>
     */
    public function publik(): array
    {
        return Cache::remember('statistik.publik', 600, function () {
            $perStatus = $this->perStatus(PencariKerja::query());
            $sudahDilatih = [StatusPendaftar::Dilatih, StatusPendaftar::Bersertifikat, StatusPendaftar::NonBersertifikat];

            return [
                'terdaftar' => array_sum($perStatus),
                'dilatih' => (int) PencariKerja::whereIn('status', $sudahDilatih)
                    ->orWhereHas('pesertaPelatihan', fn ($q) => $q->whereIn('status', ['diterima', 'lulus', 'tidak_lulus']))
                    ->count(),
                'bersertifikat' => (int) PencariKerja::whereHas('pesertaPelatihan', fn ($q) => $q->whereNotNull('nomor_sertifikat'))->count(),
                'disalurkan' => $perStatus[StatusPendaftar::Disalurkan->value] ?? 0,
                'instansi' => Instansi::where('status', StatusInstansi::Terverifikasi)->count(),
            ];
        });
    }

    /**
     * Jumlah pendaftar per status (semua status selalu ada, default 0).
     *
     * @return array<string, int>
     */
    public function perStatus($query): array
    {
        $hasil = (clone $query)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return collect(StatusPendaftar::cases())
            ->mapWithKeys(fn (StatusPendaftar $s) => [$s->value => (int) ($hasil[$s->value] ?? 0)])
            ->all();
    }

    /**
     * Rekap pendaftar per kecamatan x status.
     *
     * @return Collection<int, array{kecamatan: string, total: int, status: array<string, int>}>
     */
    public function rekapKecamatan(User $user): Collection
    {
        $angka = PencariKerja::visibleTo($user)
            ->selectRaw('kecamatan_id, status, count(*) as total')
            ->groupBy('kecamatan_id', 'status')
            ->toBase()
            ->get()
            ->groupBy('kecamatan_id');

        return Kecamatan::orderBy('nama')
            ->when(! $user->isSuperAdmin(), fn ($q) => $q->whereIn('id', $user->kecamatanIds()))
            ->get(['id', 'nama'])
            ->map(function (Kecamatan $k) use ($angka) {
                $baris = $angka->get($k->id, collect())->pluck('total', 'status');
                $status = collect(StatusPendaftar::cases())->mapWithKeys(fn ($s) => [$s->value => (int) ($baris[$s->value] ?? 0)])->all();

                return ['kecamatan' => $k->nama, 'total' => array_sum($status), 'status' => $status];
            });
    }

    /**
     * Bertahan bekerja: hasil tindak lanjut bulan ke-1, 3, 6 pekerja tersalur.
     * Persen = masih bekerja / (masih bekerja + berhenti); yang tidak bisa dihubungi tidak dihitung.
     *
     * @return array{tersalur: int, bulan: array<int, array{bulan: int, masih_bekerja: int, berhenti: int, tidak_terhubung: int, persen: int|null}>}
     */
    public function retensi(User $user): array
    {
        $terlihat = fn ($q) => $q->visibleTo($user);

        $angka = TindakLanjut::query()
            ->whereHas('penyaluran.pencariKerja', $terlihat)
            ->selectRaw('bulan_ke, status, count(*) as total')
            ->groupBy('bulan_ke', 'status')
            ->toBase()
            ->get()
            ->groupBy('bulan_ke');

        $bulan = array_map(function (int $b) use ($angka) {
            $baris = $angka->get($b, collect())->pluck('total', 'status');
            $masih = (int) ($baris[StatusTindakLanjut::MasihBekerja->value] ?? 0);
            $berhenti = (int) ($baris[StatusTindakLanjut::Berhenti->value] ?? 0);

            return [
                'bulan' => $b,
                'masih_bekerja' => $masih,
                'berhenti' => $berhenti,
                'tidak_terhubung' => (int) ($baris[StatusTindakLanjut::TidakTerhubung->value] ?? 0),
                'persen' => $masih + $berhenti > 0 ? (int) round($masih / ($masih + $berhenti) * 100) : null,
            ];
        }, TindakLanjut::BULAN);

        return [
            'tersalur' => Penyaluran::where('status', StatusPenyaluran::Menyalurkan)->whereHas('pencariKerja', $terlihat)->count(),
            'bulan' => $bulan,
        ];
    }

    /** @return array<string, int> */
    public function ringkasanAdmin(): array
    {
        return [
            'lowongan_aktif' => JobListing::aktif()->count(),
            'antrian_instansi' => Instansi::where('status', StatusInstansi::MenungguSurvey)->count(),
            'antrian_lowongan' => JobListing::where('status', StatusLowongan::MenungguSurvey)->count(),
            'pelatihan_dibuka' => Pelatihan::where('status', StatusPelatihan::Dibuka)->count(),
            'pelatihan_total' => Pelatihan::count(),
            'instansi_terverifikasi' => Instansi::where('status', StatusInstansi::Terverifikasi)->count(),
        ];
    }
}
