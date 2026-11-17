<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminUserService
{
    /** @param array<string, mixed> $data */
    public function simpan(array $data, ?User $user = null): User
    {
        return DB::transaction(function () use ($data, $user) {
            $atribut = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? true),
            ];
            if (! empty($data['password'])) {
                $atribut['password'] = $data['password'];
            }

            if ($user) {
                $user->update($atribut);
            } else {
                $user = User::create($atribut + ['email_verified_at' => now()]);
            }

            $role = Role::from($data['role']);
            $user->syncRoles([$role->value]);

            if ($role === Role::AdminLapangan) {
                $user->kecamatan()->sync($data['kecamatan'] ?? []);
                $user->syncPermissions(! empty($data['boleh_kelola_pelatihan']) ? ['kelola-pelatihan'] : []);
            } else {
                $user->kecamatan()->sync([]);
                $user->syncPermissions([]);
            }

            return $user;
        });
    }
}
