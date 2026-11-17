<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Akun demo per role. Semua password: "password". */
class AkunDemoSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatan = Kecamatan::orderBy('id')->get();

        $this->akun('Super Admin', 'superadmin@poliran.test', Role::SuperAdmin);
        $this->akun('Admin Survei', 'survei@poliran.test', Role::AdminSurvey);

        $lapangan = $this->akun('Admin Lapangan A', 'lapangan@poliran.test', Role::AdminLapangan);
        if ($kecamatan->first()) {
            $lapangan->kecamatan()->syncWithoutDetaching([$kecamatan->first()->id]);
        }

        $lapangan2 = $this->akun('Admin Lapangan B', 'lapangan2@poliran.test', Role::AdminLapangan);
        if ($kecamatan->get(1)) {
            $lapangan2->kecamatan()->syncWithoutDetaching([$kecamatan->get(1)->id]);
        }

        $this->akun('Instansi Demo', 'instansi@poliran.test', Role::Instansi);
        $this->akun('Pencari Kerja Demo', 'pencari@poliran.test', Role::PencariKerja);
    }

    private function akun(string $nama, string $email, Role $role): User
    {
        $user = User::firstOrCreate(['email' => $email], [
            'name' => $nama,
            'phone' => '081200000000',
            'password' => 'password',
            'email_verified_at' => now(),
            'persetujuan_data_at' => now(),
        ]);
        $user->syncRoles([$role->value]);

        return $user;
    }
}
