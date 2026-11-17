<?php

use App\Enums\Role;
use App\Enums\StatusInstansi;
use App\Models\Instansi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function dataVerifikasi(array $ubah = []): array
{
    return array_merge([
        'nama' => 'Toko Maju', 'jenis' => 'umkm', 'penanggung_jawab' => 'Pak Yohanes', 'alamat' => 'Jl. Contoh No. 1',
        'no_wa' => '081234567890', 'jenis_dokumen' => 'ktp', 'nomor_dokumen' => '5302010101010001',
        'file_dokumen' => UploadedFile::fake()->image('ktp.jpg'),
        'file_selfie' => UploadedFile::fake()->image('selfie.jpg'),
        'latitude' => '-9.8606', 'longitude' => '124.2836',
    ], $ubah);
}

beforeEach(fn () => Storage::fake('local'));

it('instansi bisa mengirim data verifikasi dan berkas tersimpan private', function () {
    $user = buatUser(Role::Instansi);

    $this->actingAs($user)->put(route('instansi.verifikasi.update'), dataVerifikasi())
        ->assertRedirect(route('instansi.dashboard'));

    $instansi = $user->fresh()->instansi;
    expect($instansi->status)->toBe(StatusInstansi::MenungguSurvey);
    Storage::disk('local')->assertExists($instansi->file_dokumen);
    Storage::disk('local')->assertExists($instansi->file_selfie);
});

it('lokasi peta wajib diisi', function () {
    $user = buatUser(Role::Instansi);
    $this->actingAs($user)->put(route('instansi.verifikasi.update'), dataVerifikasi(['latitude' => '']))
        ->assertSessionHasErrors('latitude');
});

it('admin survei bisa ACC instansi', function () {
    $instansi = Instansi::factory()->create();
    $survei = buatUser(Role::AdminSurvey);

    $this->actingAs($survei)->get(route('admin.survei-instansi.show', $instansi))->assertOk()->assertSee('Buka di Google Maps');
    $this->post(route('admin.survei-instansi.putusan', $instansi), ['keputusan' => 'terima', 'catatan_survei' => 'Sudah dicek'])
        ->assertRedirect();

    expect($instansi->fresh()->status)->toBe(StatusInstansi::Terverifikasi)
        ->and($instansi->fresh()->disurvei_oleh)->toBe($survei->id);
});

it('menolak instansi wajib alasan, lalu instansi bisa kirim ulang', function () {
    $instansi = Instansi::factory()->create();
    $this->actingAs(buatUser(Role::AdminSurvey));

    $this->post(route('admin.survei-instansi.putusan', $instansi), ['keputusan' => 'tolak'])->assertSessionHasErrors('alasan_tolak');
    $this->post(route('admin.survei-instansi.putusan', $instansi), ['keputusan' => 'tolak', 'alasan_tolak' => 'KTP buram']);
    expect($instansi->fresh()->status)->toBe(StatusInstansi::Ditolak);

    $this->actingAs($instansi->user)->get(route('instansi.dashboard'))->assertSee('KTP buram');
    $this->put(route('instansi.verifikasi.update'), dataVerifikasi(['file_dokumen' => null, 'file_selfie' => null]))->assertSessionHasNoErrors();
    expect($instansi->fresh()->status)->toBe(StatusInstansi::MenungguSurvey)
        ->and($instansi->fresh()->alasan_tolak)->toBeNull();
});

it('instansi terverifikasi tidak bisa mengubah data verifikasi', function () {
    $instansi = Instansi::factory()->terverifikasi()->create();
    $this->actingAs($instansi->user)->put(route('instansi.verifikasi.update'), dataVerifikasi())->assertForbidden();
});

it('berkas instansi hanya bisa dibuka pemilik dan petugas survei', function () {
    Storage::disk('local')->put('instansi/dokumen/a.jpg', 'isi');
    $instansi = Instansi::factory()->create(['file_dokumen' => 'instansi/dokumen/a.jpg']);
    $url = route('berkas.instansi', [$instansi, 'dokumen']);

    $this->actingAs($instansi->user)->get($url)->assertOk();
    $this->actingAs(buatUser(Role::AdminSurvey))->get($url)->assertOk();
    $this->actingAs(buatUser(Role::Instansi))->get($url)->assertForbidden();
    $this->actingAs(buatUser(Role::PencariKerja))->get($url)->assertForbidden();
    $this->actingAs(buatUser(Role::AdminLapangan))->get($url)->assertForbidden();
});

it('admin lapangan dan instansi tidak bisa membuka antrian survei', function () {
    $this->actingAs(buatUser(Role::AdminLapangan))->get(route('admin.survei-instansi.index'))->assertForbidden();
    $this->actingAs(buatUser(Role::Instansi))->get(route('admin.survei-instansi.index'))->assertForbidden();
});
