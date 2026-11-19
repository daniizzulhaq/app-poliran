<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;

/** Menu sidebar dibangun dari permission user. Item yang rutenya belum ada otomatis disembunyikan. */
class Menu
{
    /**
     * [judul grup => [[label, route, permission|null, pola aktif]]]
     */
    private const ITEMS = [
        'Utama' => [
            ['Dashboard', 'dashboard', null, 'dashboard|admin.dashboard|instansi.dashboard|pencari.dashboard'],
        ],
        'Survei' => [
            ['Survei Instansi', 'admin.survei-instansi.index', 'survei-instansi', 'admin.survei-instansi.*'],
            ['Survei Lowongan', 'admin.survei-lowongan.index', 'survei-lowongan', 'admin.survei-lowongan.*'],
        ],
        'Pendaftar & Penyaluran' => [
            ['Pendaftar', 'admin.pendaftar.index', 'kelola-pendaftar', 'admin.pendaftar.*'],
            ['Lowongan', 'admin.lowongan.index', 'lihat-lowongan', 'admin.lowongan.*'],
            ['Penyaluran', 'admin.penyaluran.index', 'kelola-penyaluran', 'admin.penyaluran.index'],
            ['Tindak Lanjut Kerja', 'admin.penyaluran.tindak-lanjut', 'kelola-penyaluran', 'admin.penyaluran.tindak-lanjut*'],
        ],
        'Pelatihan' => [
            ['Pelatihan', 'admin.pelatihan.index', 'kelola-pelatihan', 'admin.pelatihan.*'],
            ['Peserta Pelatihan', 'admin.peserta.index', 'kelola-peserta-pelatihan', 'admin.peserta.*'],
        ],
        'Instansi' => [
            ['Data Verifikasi', 'instansi.verifikasi.edit', 'kelola-lowongan-sendiri', 'instansi.verifikasi.*'],
            ['Lowongan Saya', 'instansi.lowongan.index', 'kelola-lowongan-sendiri', 'instansi.lowongan.*'],
        ],
        'Akun Saya' => [
            ['Profil & CV', 'pencari.profil.edit', 'daftar-pelatihan', 'pencari.profil.*'],
            ['Pelatihan Saya', 'pencari.pelatihan.index', 'daftar-pelatihan', 'pencari.pelatihan.*'],
        ],
        'Laporan' => [
            ['Laporan & Export', 'admin.laporan.index', 'lihat-laporan', 'admin.laporan.*'],
        ],
        'Pengaturan' => [
            ['Akun Admin', 'admin.users.index', 'kelola-admin', 'admin.users.*'],
            ['Kecamatan & Desa', 'admin.kecamatan.index', 'kelola-master-data', 'admin.kecamatan.*|admin.desa.*'],
            ['Kategori', 'admin.kategori.index', 'kelola-master-data', 'admin.kategori.*'],
            ['Skill', 'admin.skill.index', 'kelola-master-data', 'admin.skill.*'],
            ['Konten Halaman Awal', 'admin.konten.edit', 'kelola-konten', 'admin.konten.*'],
            ['Cerita Sukses', 'admin.testimoni.index', 'kelola-konten', 'admin.testimoni.*'],
        ],
    ];

    /**
     * @return array<string, array<int, array{label: string, url: string, active: bool}>>
     */
    public static function for(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $hasil = [];
        foreach (self::ITEMS as $grup => $items) {
            foreach ($items as [$label, $route, $permission, $pola]) {
                if (! Route::has($route) || ($permission && ! $user->can($permission))) {
                    continue;
                }
                $hasil[$grup][] = [
                    'label' => $label,
                    'url' => route($route),
                    'active' => request()->routeIs(...explode('|', $pola)),
                ];
            }
        }

        return $hasil;
    }
}
