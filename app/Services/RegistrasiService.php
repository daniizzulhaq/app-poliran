<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegistrasiService
{
    /** @param array{name: string, email: string, phone: string, password: string} $data */
    public function daftar(array $data, Role $role): User
    {
        return DB::transaction(function () use ($data, $role) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
                'persetujuan_data_at' => now(),
            ]);
            $user->assignRole($role->value);

            return $user;
        });
    }
}
