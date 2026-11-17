<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            WilayahSeeder::class,
            MasterDataSeeder::class,
            AkunDemoSeeder::class,
        ]);

        // Data contoh (instansi, lowongan, pelatihan, pendaftar) hanya untuk lokal.
        if (app()->environment('local')) {
            $this->call(DataDemoSeeder::class);
        }
    }
}
