<?php

use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\Kecamatan;
use App\Models\Pelatihan;
use App\Models\PencariKerja;
use App\Models\User;
use Database\Seeders\AkunDemoSeeder;
use Database\Seeders\DataDemoSeeder;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Support\Facades\Storage;

/** Membuka semua halaman dengan data demo untuk tiap akun: tidak boleh ada error 500. */
it('semua halaman bisa dibuka tanpa error untuk setiap akun demo', function () {
    Storage::fake('local');
    $this->seed([WilayahSeeder::class, MasterDataSeeder::class, AkunDemoSeeder::class, DataDemoSeeder::class]);

    $lowongan = JobListing::first();
    $pencari = PencariKerja::first();
    $pelatihan = Pelatihan::first();
    $instansi = Instansi::first();
    $kecamatan = Kecamatan::first();

    $halaman = [
        route('home'), route('public.pelatihan.index'), route('public.pelatihan.show', $pelatihan), route('public.tentang'),
        route('public.kontak'), route('dashboard'), route('akun.password.edit'),
        route('admin.dashboard'), route('admin.survei-instansi.index'), route('admin.survei-instansi.show', $instansi),
        route('admin.survei-lowongan.index'), route('admin.lowongan.index'), route('admin.lowongan.show', $lowongan),
        route('admin.pendaftar.index'), route('admin.pendaftar.create'), route('admin.pendaftar.show', $pencari),
        route('admin.pendaftar.edit', $pencari), route('admin.penyaluran.index'), route('admin.pelatihan.index'),
        route('admin.pelatihan.create'), route('admin.pelatihan.show', $pelatihan), route('admin.pelatihan.edit', $pelatihan),
        route('admin.peserta.index'), route('admin.peserta.pelatihan', $pelatihan), route('admin.konten.edit'),
        route('admin.testimoni.index'), route('admin.testimoni.create'), route('admin.laporan.index'), route('admin.users.index'),
        route('admin.users.create'), route('admin.kecamatan.index'), route('admin.kecamatan.show', $kecamatan),
        route('admin.kategori.index'), route('admin.skill.index'),
        route('instansi.dashboard'), route('instansi.verifikasi.edit'), route('instansi.lowongan.index'),
        route('instansi.lowongan.create'), route('instansi.lowongan.show', $lowongan),
        route('pencari.dashboard'), route('pencari.profil.edit'), route('pencari.pelatihan.index'),
        route('pencari.pelatihan.daftar', $pelatihan),
    ];

    // Ambil user satu per satu (seperti saat login sungguhan).
    foreach (User::pluck('id') as $id) {
        $user = User::find($id);
        $this->actingAs($user);
        foreach ($halaman as $url) {
            $res = $this->get($url);
            $status = $res->getStatusCode();
            if ($status >= 500) {
                fwrite(STDERR, $res->exception?->getMessage().' @ '.$res->exception?->getFile().':'.$res->exception?->getLine());
            }
            expect($status)->toBeLessThan(500, "{$user->email} -> {$url} ({$status})")
                ->and($status)->not->toBe(404, "{$user->email} -> {$url} (404)");
        }
    }
});
