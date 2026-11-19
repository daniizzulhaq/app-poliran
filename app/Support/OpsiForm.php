<?php

namespace App\Support;

use App\Enums\JenisKelamin;
use App\Enums\Pendidikan;
use App\Enums\TipeKerja;
use App\Enums\WilayahKerja;
use App\Models\Kategori;
use App\Models\Kecamatan;
use App\Models\Skill;
use App\Models\User;

/** Pilihan dropdown yang dipakai berulang di form. */
class OpsiForm
{
    /** @return array<string, array<int|string, string>> */
    public static function pendaftar(?User $adminLapangan = null): array
    {
        $kecamatan = Kecamatan::orderBy('nama')
            ->when($adminLapangan && ! $adminLapangan->isSuperAdmin(), fn ($q) => $q->whereIn('id', $adminLapangan->kecamatanIds()))
            ->pluck('nama', 'id')
            ->all();

        return [
            'kecamatanOptions' => $kecamatan,
            'kategoriOptions' => Kategori::orderBy('nama')->pluck('nama', 'id')->all(),
            'skillOptions' => Skill::orderBy('nama')->pluck('nama', 'id')->all(),
            'pendidikanOptions' => Pendidikan::options(),
            'jenisKelaminOptions' => JenisKelamin::options(),
        ];
    }

    /** @return array<string, array<int|string, string>> pilihan form lowongan (instansi & petugas) */
    public static function lowongan(): array
    {
        return [
            'kategori' => Kategori::orderBy('nama')->pluck('nama', 'id')->all(),
            'skills' => Skill::orderBy('nama')->pluck('nama', 'id')->all(),
            'pendidikan' => Pendidikan::options(),
            'jenisKelamin' => JenisKelamin::options(),
            'tipeKerja' => TipeKerja::options(),
            'wilayahKerja' => WilayahKerja::options(),
        ];
    }
}
