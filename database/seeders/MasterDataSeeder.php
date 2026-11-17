<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'IT & Komputer', 'Bangunan', 'Pertanian', 'Peternakan', 'Perdagangan & Penjualan',
            'Jasa & Perhotelan', 'Otomotif', 'Menjahit & Tata Busana', 'Kuliner', 'Administrasi',
        ];
        foreach ($kategori as $nama) {
            Kategori::firstOrCreate(['nama' => $nama]);
        }

        $skill = [
            'Komputer Dasar', 'Microsoft Office', 'Desain Grafis', 'Tukang Batu', 'Tukang Kayu', 'Pengelasan',
            'Instalasi Listrik', 'Bertani', 'Beternak', 'Servis Motor', 'Mengemudi (SIM A)', 'Mengemudi (SIM C)',
            'Menjahit', 'Memasak', 'Penjualan', 'Melayani Pelanggan',
        ];
        foreach ($skill as $nama) {
            Skill::firstOrCreate(['nama' => $nama]);
        }
    }
}
