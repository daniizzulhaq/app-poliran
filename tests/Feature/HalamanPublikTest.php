<?php

use App\Enums\Role;
use App\Enums\StatusPelatihan;
use App\Models\JobListing;
use App\Models\Pelatihan;
use App\Models\Testimoni;
use App\Services\PengaturanService;
use Illuminate\Support\Facades\Cache;

it('halaman awal tampil lengkap dengan lowongan tersedia', function () {
    buatKecamatan();
    JobListing::factory()->aktif()->create(['judul' => 'Lowongan Pekerjaan Terbaru']);
    Pelatihan::factory()->create(['nama' => 'Pelatihan Las Dibuka']);
    Pelatihan::factory()->create(['nama' => 'Pelatihan Masih Draft', 'status' => StatusPelatihan::Draft]);
    Testimoni::create(['nama' => 'Yohana', 'isi' => 'Sekarang saya bekerja', 'aktif' => true]);

    $this->get(route('home'))->assertOk()
        ->assertSee('Polisi Peduli Pengangguran')
        ->assertSee('GRATIS', false)
        ->assertSee('Untuk Siapa POLIRAN?')
        ->assertSee('Usia minimal 18 tahun.')
        ->assertSeeInOrder(['Tentang POLIRAN', 'Lowongan Pekerjaan Tersedia', 'Pelatihan yang Tersedia', 'Untuk Siapa POLIRAN?', 'Pencari Kerja', 'Pencari Pelatihan', 'Penyedia Kerja'])
        ->assertSee('memeriksa kompetensi Anda')->assertDontSee('kecocokan kemampuan')
        ->assertSee('warga sudah disalurkan kerja')
        ->assertSee('Sambutan Kapolres')
        ->assertSee('Pelatihan Las Dibuka')->assertDontSee('Pelatihan Masih Draft')
        ->assertSee('Cara Daftar')
        ->assertSee('Yohana')
        ->assertDontSee('Kontak Layanan')
        ->assertSee('Hati-hati Calo Kerja')->assertSee('110')
        ->assertSee('Lowongan Pekerjaan Tersedia')->assertSee('Lowongan Pekerjaan Terbaru');
});

it('halaman tentang, kontak, dan pelatihan bisa dibuka tamu', function () {
    $p = Pelatihan::factory()->create();
    $this->get(route('public.tentang'))->assertOk();
    $this->get(route('public.kontak'))->assertOk();
    $this->get(route('public.pelatihan.index'))->assertOk()->assertSee($p->nama)->assertSee('Usia minimal 18 tahun.');
    $this->get(route('public.pelatihan.show', $p))->assertOk()->assertSee('Usia minimal 18 tahun.');
});

it('pelatihan draft tidak bisa dibuka publik', function () {
    $p = Pelatihan::factory()->create(['status' => StatusPelatihan::Draft]);
    $this->get(route('public.pelatihan.show', $p))->assertNotFound();
});

it('super admin bisa mengubah konten halaman awal', function () {
    Cache::flush();
    $this->actingAs(buatUser(Role::SuperAdmin))->put(route('admin.konten.update'), [
        'hero_judul' => 'Judul Baru Uji', 'hero_teks' => 'Teks hero', 'sambutan_nama' => 'Nama Kapolres Uji',
        'sambutan_pangkat' => 'AKBP', 'sambutan_isi' => 'Isi sambutan', 'program_teks' => 'Penjelasan',
        'kontak_alamat' => 'Alamat uji', 'kontak_wa' => '081200000001',
    ])->assertSessionHasNoErrors();

    expect(app(PengaturanService::class)->get('hero_judul'))->toBe('Judul Baru Uji');
    $this->get(route('home'))->assertSee('Judul Baru Uji')->assertSee('Nama Kapolres Uji');
});

it('selain super admin tidak bisa mengubah konten', function () {
    $this->actingAs(buatUser(Role::AdminSurvey))->get(route('admin.konten.edit'))->assertForbidden();
    $this->actingAs(buatUser(Role::AdminLapangan))->get(route('admin.testimoni.index'))->assertForbidden();
});

it('super admin bisa menambah cerita sukses', function () {
    $this->actingAs(buatUser(Role::SuperAdmin))->post(route('admin.testimoni.store'), [
        'nama' => 'Markus', 'isi' => 'Terima kasih POLIRAN', 'aktif' => '1', 'urutan' => 1,
    ])->assertRedirect(route('admin.testimoni.index'));
    expect(Testimoni::where('nama', 'Markus')->exists())->toBeTrue();
});
