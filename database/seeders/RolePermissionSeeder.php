<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /** permission => role yang otomatis memilikinya */
    public const PERMISSIONS = [
        'kelola-admin' => ['super_admin'],
        'kelola-master-data' => ['super_admin'],
        'kelola-konten' => ['super_admin'],
        'lihat-laporan' => ['super_admin'],
        'survei-instansi' => ['super_admin', 'admin_survey'],
        'survei-lowongan' => ['super_admin', 'admin_survey'],
        'lihat-lowongan' => ['super_admin', 'admin_survey', 'admin_lapangan'],
        'kelola-pendaftar' => ['super_admin', 'admin_lapangan'],
        'kelola-penyaluran' => ['super_admin', 'admin_lapangan'],
        'kelola-pelatihan' => ['super_admin'],
        'kelola-peserta-pelatihan' => ['super_admin', 'admin_lapangan'],
        'kelola-lowongan-sendiri' => ['instansi'],
        'daftar-pelatihan' => ['pencari_kerja'],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (RoleEnum::cases() as $role) {
            Role::findOrCreate($role->value, 'web');
        }

        foreach (self::PERMISSIONS as $permission => $roles) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach (RoleEnum::cases() as $role) {
            $milik = array_keys(array_filter(self::PERMISSIONS, fn ($roles) => in_array($role->value, $roles, true)));
            Role::findByName($role->value, 'web')->syncPermissions($milik);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
