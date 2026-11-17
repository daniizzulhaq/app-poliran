<?php

use App\Enums\Role;
use App\Enums\StatusPelatihan;
use App\Enums\StatusPendaftar;
use App\Enums\StatusPeserta;
use App\Models\Kategori;
use App\Models\Pelatihan;
use App\Models\PencariKerja;
use App\Models\PesertaPelatihan;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
    $this->kecA = buatKecamatan('Kec A');
    $this->kecB = buatKecamatan('Kec B');
});

function pencariBerakun($kecamatan): PencariKerja
{
    $user = buatUser(Role::PencariKerja);

    return PencariKerja::factory()->diKecamatan($kecamatan)->create(['user_id' => $user->id]);
}

it('super admin membuat pelatihan (draft) lalu membukanya', function () {
    $admin = buatUser(Role::SuperAdmin);
    $kategori = Kategori::create(['nama' => 'Otomotif']);

    $this->actingAs($admin)->post(route('admin.pelatihan.store'), [
        'nama' => 'Servis Motor Dasar', 'kategori_id' => $kategori->id, 'deskripsi' => 'Belajar servis motor',
        'penyelenggara' => 'Polres Timor Tengah Selatan', 'mitra' => 'Bengkel Mitra', 'tanggal_mulai' => now()->addWeek()->toDateString(),
        'jam_mulai' => '08:00', 'kecamatan_id' => $this->kecA->id, 'alamat' => 'Aula', 'kuota' => 10,
        'batas_pendaftaran' => now()->addDays(3)->toDateString(), 'bersertifikat' => '1',
    ])->assertRedirect();

    $p = Pelatihan::first();
    expect($p->status)->toBe(StatusPelatihan::Draft)->and($p->slug)->toBe('servis-motor-dasar');
    $this->get(route('public.pelatihan.index'))->assertDontSee('Servis Motor Dasar');

    $this->post(route('admin.pelatihan.status', $p), ['status' => 'dibuka']);
    $this->get(route('public.pelatihan.index'))->assertSee('Servis Motor Dasar');
    $this->get(route('public.pelatihan.show', $p->fresh()))->assertOk()->assertSee('Bengkel Mitra');
});

it('admin lapangan tanpa izin tidak bisa membuat pelatihan, dengan izin bisa', function () {
    $admin = buatUser(Role::AdminLapangan, [$this->kecA->id]);
    $this->actingAs($admin)->get(route('admin.pelatihan.create'))->assertForbidden();

    $admin->givePermissionTo('kelola-pelatihan');
    $this->actingAs($admin->fresh())->get(route('admin.pelatihan.create'))->assertOk();
});

it('tamu diarahkan masuk saat membuka form daftar pelatihan', function () {
    $p = Pelatihan::factory()->create();
    $this->get(route('pencari.pelatihan.daftar', $p))->assertRedirect(route('login'));
});

it('pencari kerja tanpa profil diarahkan isi profil lalu kembali ke form', function () {
    $p = Pelatihan::factory()->create();
    $user = buatUser(Role::PencariKerja);
    Kategori::firstOrCreate(['nama' => 'Umum']);

    $this->actingAs($user)->get(route('pencari.pelatihan.daftar', $p))->assertRedirect(route('pencari.profil.edit'));

    $this->put(route('pencari.profil.update'), [
        'nama' => 'Ani', 'nik' => '5302014101990009', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '1999-01-01',
        'pendidikan' => 'sma', 'no_wa' => '081234567890', 'kecamatan_id' => $this->kecA->id,
        'desa_id' => $this->kecA->desa()->first()->id, 'alamat_detail' => 'RT 1', 'kategori' => [Kategori::first()->id],
    ])->assertRedirect(route('pencari.pelatihan.daftar', $p));
});

it('pendaftaran pelatihan dua kali ditolak', function () {
    $p = Pelatihan::factory()->create();
    $pencari = pencariBerakun($this->kecA);
    $this->actingAs($pencari->user);

    $this->post(route('pencari.pelatihan.store', $p), ['alasan' => 'Ingin bisa'])->assertSessionHasNoErrors();
    $this->post(route('pencari.pelatihan.store', $p), ['alasan' => 'Lagi'])->assertSessionHasErrors('alasan');

    expect(PesertaPelatihan::count())->toBe(1);
});

it('tidak bisa daftar pelatihan yang belum dibuka atau lewat batas', function () {
    $draft = Pelatihan::factory()->create(['status' => StatusPelatihan::Draft]);
    $lewat = Pelatihan::factory()->create(['batas_pendaftaran' => now()->subDay()->toDateString()]);
    $pencari = pencariBerakun($this->kecA);
    $this->actingAs($pencari->user);

    $this->post(route('pencari.pelatihan.store', $draft), ['alasan' => 'x'])->assertSessionHasErrors('alasan');
    $this->post(route('pencari.pelatihan.store', $lewat), ['alasan' => 'x'])->assertSessionHasErrors('alasan');
});

it('diterima -> dilatih, lulus bersertifikat -> bersertifikat + PDF yang bisa diunduh', function () {
    $p = Pelatihan::factory()->create(['bersertifikat' => true]);
    $pencari = pencariBerakun($this->kecA);
    $peserta = PesertaPelatihan::create(['pelatihan_id' => $p->id, 'pencari_kerja_id' => $pencari->id, 'alasan' => 'x', 'status' => 'mendaftar']);
    $admin = buatUser(Role::AdminLapangan, [$this->kecA->id]);

    $this->actingAs($admin)->put(route('admin.peserta.seleksi', $peserta), ['keputusan' => 'diterima'])->assertSessionHasNoErrors();
    expect($pencari->fresh()->status)->toBe(StatusPendaftar::Dilatih);

    $this->put(route('admin.peserta.hasil', $peserta), ['hasil' => 'lulus'])->assertSessionHasNoErrors();
    $peserta->refresh();
    expect($peserta->status)->toBe(StatusPeserta::Lulus)
        ->and($peserta->nomor_sertifikat)->not->toBeNull()
        ->and($pencari->fresh()->status)->toBe(StatusPendaftar::Bersertifikat);
    Storage::disk('local')->assertExists($peserta->sertifikat_path);

    $this->actingAs($pencari->user)->get(route('berkas.sertifikat', $peserta))
        ->assertOk()->assertHeader('content-type', 'application/pdf');
    $this->actingAs(pencariBerakun($this->kecA)->user)->get(route('berkas.sertifikat', $peserta))->assertForbidden();
});

it('tidak lulus -> non bersertifikat', function () {
    $p = Pelatihan::factory()->create();
    $pencari = pencariBerakun($this->kecA);
    $peserta = PesertaPelatihan::create(['pelatihan_id' => $p->id, 'pencari_kerja_id' => $pencari->id, 'alasan' => 'x', 'status' => 'diterima']);

    $this->actingAs(buatUser(Role::SuperAdmin))->put(route('admin.peserta.hasil', $peserta), ['hasil' => 'tidak_lulus']);
    expect($pencari->fresh()->status)->toBe(StatusPendaftar::NonBersertifikat)
        ->and($peserta->fresh()->nomor_sertifikat)->toBeNull();
});

it('lulus pada pelatihan tanpa sertifikat -> non bersertifikat', function () {
    $p = Pelatihan::factory()->create(['bersertifikat' => false]);
    $pencari = pencariBerakun($this->kecA);
    $peserta = PesertaPelatihan::create(['pelatihan_id' => $p->id, 'pencari_kerja_id' => $pencari->id, 'alasan' => 'x', 'status' => 'diterima']);

    $this->actingAs(buatUser(Role::SuperAdmin))->put(route('admin.peserta.hasil', $peserta), ['hasil' => 'lulus']);
    expect($pencari->fresh()->status)->toBe(StatusPendaftar::NonBersertifikat)
        ->and($peserta->fresh()->nomor_sertifikat)->toBeNull();
});

it('admin lapangan hanya mengelola peserta dari kecamatannya', function () {
    $p = Pelatihan::factory()->create();
    $pencariB = pencariBerakun($this->kecB);
    $pencariB->update(['nama' => 'Peserta Kecamatan B']);
    $peserta = PesertaPelatihan::create(['pelatihan_id' => $p->id, 'pencari_kerja_id' => $pencariB->id, 'alasan' => 'x', 'status' => 'mendaftar']);
    $adminA = buatUser(Role::AdminLapangan, [$this->kecA->id]);

    $this->actingAs($adminA)->get(route('admin.peserta.pelatihan', $p))->assertOk()->assertDontSee('Peserta Kecamatan B');
    $this->put(route('admin.peserta.seleksi', $peserta), ['keputusan' => 'diterima'])->assertForbidden();
    expect($peserta->fresh()->status)->toBe(StatusPeserta::Mendaftar);
});

it('kuota pelatihan tidak bisa dilampaui', function () {
    $p = Pelatihan::factory()->create(['kuota' => 1]);
    $a = PesertaPelatihan::create(['pelatihan_id' => $p->id, 'pencari_kerja_id' => pencariBerakun($this->kecA)->id, 'alasan' => 'x', 'status' => 'mendaftar']);
    $b = PesertaPelatihan::create(['pelatihan_id' => $p->id, 'pencari_kerja_id' => pencariBerakun($this->kecA)->id, 'alasan' => 'x', 'status' => 'mendaftar']);
    $this->actingAs(buatUser(Role::SuperAdmin));

    $this->put(route('admin.peserta.seleksi', $a), ['keputusan' => 'diterima'])->assertSessionHasNoErrors();
    $this->put(route('admin.peserta.seleksi', $b), ['keputusan' => 'diterima'])->assertSessionHasErrors('status');
});

it('setelah pelatihan, pendaftar cocok dengan lowongan sesuai kategori pelatihan', function () {
    $kategori = Kategori::create(['nama' => 'Kuliner']);
    $p = Pelatihan::factory()->create(['kategori_id' => $kategori->id]);
    $pencari = pencariBerakun($this->kecA);
    PesertaPelatihan::create(['pelatihan_id' => $p->id, 'pencari_kerja_id' => $pencari->id, 'alasan' => 'x', 'status' => 'lulus']);
    $lowongan = \App\Models\JobListing::factory()->aktif()->create(['kategori_id' => $kategori->id]);

    $hasil = app(\App\Services\JobMatcher::class)->pendaftarCocok($lowongan, buatUser(Role::SuperAdmin));
    expect($hasil->pluck('pencari.id')->all())->toBe([$pencari->id])
        ->and($hasil->first()['rincian']['kategori'])->toBeTrue();
});
