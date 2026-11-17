<?php

use App\Enums\Role;
use App\Enums\StatusPendaftar;
use App\Models\Kategori;
use App\Models\PencariKerja;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->kecA = buatKecamatan('Kecamatan A');
    $this->kecB = buatKecamatan('Kecamatan B');
    $this->kategori = Kategori::create(['nama' => 'Pertanian']);
});

function dataProfil($kecamatan, array $ubah = []): array
{
    return array_merge([
        'nama' => 'Maria', 'nik' => '5302014101990001', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '1999-01-01',
        'pendidikan' => 'sma', 'no_wa' => '081234567890', 'kecamatan_id' => $kecamatan->id,
        'desa_id' => $kecamatan->desa()->first()->id, 'alamat_detail' => 'RT 01',
        'kategori' => [Kategori::first()->id],
    ], $ubah);
}

it('pencari kerja bisa mengisi profil dan otomatis berstatus terdaftar', function () {
    $user = buatUser(Role::PencariKerja);
    $this->actingAs($user)->put(route('pencari.profil.update'), dataProfil($this->kecA, [
        'cv' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
    ]))->assertRedirect(route('pencari.dashboard'));

    $pencari = $user->fresh()->pencariKerja;
    expect($pencari->status)->toBe(StatusPendaftar::Terdaftar)
        ->and($pencari->riwayatStatus)->toHaveCount(1);
    Storage::disk('local')->assertExists($pencari->cv_path);

    $this->actingAs($user->fresh())->get(route('pencari.dashboard'))->assertOk()->assertSee('Status saat ini');
});

it('desa harus milik kecamatan yang dipilih', function () {
    $this->actingAs(buatUser(Role::PencariKerja))
        ->put(route('pencari.profil.update'), dataProfil($this->kecA, ['desa_id' => $this->kecB->desa()->first()->id]))
        ->assertSessionHasErrors('desa_id');
});

it('NIK duplikat ditolak', function () {
    PencariKerja::factory()->diKecamatan($this->kecA)->create(['nik' => '5302014101990001']);
    $this->actingAs(buatUser(Role::PencariKerja))
        ->put(route('pencari.profil.update'), dataProfil($this->kecA))
        ->assertSessionHasErrors('nik');
});

it('NIK disensor di tampilan admin', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create(['nik' => '5302014101990001']);
    $this->actingAs(buatUser(Role::SuperAdmin))->get(route('admin.pendaftar.show', $p))
        ->assertSee('5302********0001')->assertDontSee('5302014101990001');
});

it('pendaftar otomatis masuk daftar admin lapangan kecamatannya saja', function () {
    $a = PencariKerja::factory()->diKecamatan($this->kecA)->create(['nama' => 'Warga Kecamatan A']);
    $b = PencariKerja::factory()->diKecamatan($this->kecB)->create(['nama' => 'Warga Kecamatan B']);
    $adminA = buatUser(Role::AdminLapangan, [$this->kecA->id]);

    $this->actingAs($adminA)->get(route('admin.pendaftar.index'))
        ->assertSee('Warga Kecamatan A')->assertDontSee('Warga Kecamatan B');

    $this->actingAs(buatUser(Role::SuperAdmin))->get(route('admin.pendaftar.index'))
        ->assertSee('Warga Kecamatan A')->assertSee('Warga Kecamatan B');
});

it('admin lapangan kecamatan A tidak bisa melihat, mengubah, atau mengunduh CV pendaftar kecamatan B', function () {
    Storage::disk('local')->put('cv/b.pdf', '%PDF');
    $b = PencariKerja::factory()->diKecamatan($this->kecB)->create(['cv_path' => 'cv/b.pdf']);
    $adminA = buatUser(Role::AdminLapangan, [$this->kecA->id]);
    $this->actingAs($adminA);

    $this->get(route('admin.pendaftar.show', $b))->assertForbidden();
    $this->get(route('admin.pendaftar.edit', $b))->assertForbidden();
    $this->put(route('admin.pendaftar.update', $b), dataProfil($this->kecB, ['nik' => $b->nik]))->assertForbidden();
    $this->post(route('admin.pendaftar.status', $b), ['status' => 'direkrut', 'catatan' => 'x'])->assertForbidden();
    $this->get(route('berkas.cv', $b))->assertForbidden();

    expect($b->fresh()->status)->toBe(StatusPendaftar::Terdaftar);

    $adminB = buatUser(Role::AdminLapangan, [$this->kecB->id]);
    $this->actingAs($adminB)->get(route('berkas.cv', $b))->assertOk();
});

it('admin lapangan bisa menambah pendaftar manual hanya di kecamatannya', function () {
    $adminA = buatUser(Role::AdminLapangan, [$this->kecA->id]);
    $this->actingAs($adminA);

    $this->post(route('admin.pendaftar.store'), dataProfil($this->kecB))->assertSessionHasErrors('kecamatan_id');
    $this->post(route('admin.pendaftar.store'), dataProfil($this->kecA, ['no_wa' => '']))->assertRedirect();

    $p = PencariKerja::first();
    expect($p->user_id)->toBeNull()->and($p->dibuat_oleh)->toBe($adminA->id);
});

it('ubah status tersimpan di riwayat', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $adminA = buatUser(Role::AdminLapangan, [$this->kecA->id]);

    $this->actingAs($adminA)->post(route('admin.pendaftar.status', $p), ['status' => 'direkrut', 'catatan' => 'Sudah diwawancara'])
        ->assertRedirect();

    $riwayat = $p->riwayatStatus()->first();
    expect($p->fresh()->status)->toBe(StatusPendaftar::Direkrut)
        ->and($riwayat->status_lama)->toBe(StatusPendaftar::Terdaftar)
        ->and($riwayat->status_baru)->toBe(StatusPendaftar::Direkrut)
        ->and($riwayat->user_id)->toBe($adminA->id)
        ->and($riwayat->catatan)->toBe('Sudah diwawancara');
});

it('pencari kerja hanya bisa membuka CV miliknya', function () {
    Storage::disk('local')->put('cv/x.pdf', '%PDF');
    $user = buatUser(Role::PencariKerja);
    $milik = PencariKerja::factory()->diKecamatan($this->kecA)->create(['user_id' => $user->id, 'cv_path' => 'cv/x.pdf']);
    $lain = PencariKerja::factory()->diKecamatan($this->kecA)->create(['cv_path' => 'cv/x.pdf']);

    $this->actingAs($user)->get(route('berkas.cv', $milik))->assertOk();
    $this->get(route('berkas.cv', $lain))->assertForbidden();
});
