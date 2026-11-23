<?php

use App\Enums\Role;
use App\Enums\StatusInstansi;
use App\Models\Instansi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

function dataVerifikasi(array $ubah = []): array
{
    return array_merge([
        'nama' => 'Toko Maju', 'jenis' => 'umkm', 'penanggung_jawab' => 'Pak Yohanes', 'alamat' => 'Jl. Contoh No. 1',
        'no_wa' => '081234567890', 'tanggal_lahir' => '1990-01-01',
        'latitude' => '-9.8606', 'longitude' => '124.2836',
    ], $ubah);
}

beforeEach(fn () => Storage::fake('local'));

it('instansi bisa mengirim data verifikasi tanpa dokumen dan selfie', function () {
    $user = buatUser(Role::Instansi);

    $this->actingAs($user)->put(route('instansi.verifikasi.update'), dataVerifikasi())
        ->assertRedirect(route('instansi.dashboard'));

    $instansi = $user->fresh()->instansi;
    expect($instansi->status)->toBe(StatusInstansi::MenungguSurvey)
        ->and($instansi->tanggal_lahir->toDateString())->toBe('1990-01-01')
        ->and($instansi->jenis_dokumen)->toBeNull()
        ->and($instansi->nomor_dokumen)->toBeNull()
        ->and($instansi->file_dokumen)->toBeNull()
        ->and($instansi->file_selfie)->toBeNull();
    Storage::disk('local')->assertDirectoryEmpty('/');

    $this->actingAs($user->fresh())->get(route('instansi.verifikasi.edit'))->assertOk()->assertSee('value="1990-01-01"', false);
    $this->actingAs(buatUser(Role::AdminSurvey))->get(route('admin.survei-instansi.show', $instansi))
        ->assertOk()->assertSee('1 Januari 1990')->assertDontSee('Buka berkas');

    $instansi->update(['status' => StatusInstansi::Terverifikasi]);
    $this->actingAs($user->fresh())->get(route('instansi.verifikasi.edit'))->assertOk()->assertSee('1 Januari 1990');
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
    $this->put(route('instansi.verifikasi.update'), dataVerifikasi())->assertSessionHasNoErrors();
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

it('petugas survei bisa menginput instansi jemput bola tanpa akun dan langsung terverifikasi', function () {
    $petugas = buatUser(Role::AdminSurvey);
    $this->actingAs($petugas);

    $this->get(route('admin.survei-instansi.create'))->assertOk()->assertSee('Tulis nama lengkap sesuai KTP.')
        ->assertDontSee('name="file_dokumen"', false)->assertDontSee('name="file_selfie"', false);
    $this->post(route('admin.survei-instansi.store'), dataVerifikasi())->assertSessionHasErrors('catatan_survei');
    $this->post(route('admin.survei-instansi.store'), dataVerifikasi(['catatan_survei' => 'Dikunjungi, usaha ada']))->assertSessionHasNoErrors();

    $instansi = Instansi::firstOrFail();
    expect($instansi->user_id)->toBeNull()
        ->and($instansi->dibuat_oleh)->toBe($petugas->id)
        ->and($instansi->status)->toBe(StatusInstansi::Terverifikasi)
        ->and($instansi->disurvei_oleh)->toBe($petugas->id);
    expect($instansi->file_dokumen)->toBeNull()->and($instansi->file_selfie)->toBeNull();

    $this->get(route('admin.survei-instansi.index', ['status' => 'terverifikasi']))->assertOk()->assertSee('Tanpa akun (diinput petugas)');
    $this->get(route('admin.survei-instansi.show', $instansi))->assertOk()->assertSee('+ Input Lowongan');
    $this->get(route('berkas.instansi', [$instansi, 'selfie']))->assertNotFound();
});

it('form verifikasi hanya meminta identitas tertulis dengan pengingat usia dan KTP', function () {
    $this->actingAs(buatUser(Role::Instansi))->get(route('instansi.verifikasi.edit'))->assertOk()
        ->assertSee('Tulis nama lengkap sesuai KTP.')
        ->assertSee('name="tanggal_lahir"', false)
        ->assertSee('sudah memiliki KTP')
        ->assertDontSee('name="jenis_dokumen"', false)
        ->assertDontSee('name="nomor_dokumen"', false)
        ->assertDontSee('name="file_dokumen"', false)
        ->assertDontSee('name="file_selfie"', false);
});

it('tanggal lahir penanggung jawab divalidasi saat verifikasi dan input petugas', function (?string $tanggalLahir, bool $diterima) {
    $this->travelTo(Carbon::parse('2026-11-23 12:00:00'));
    $data = dataVerifikasi(['tanggal_lahir' => $tanggalLahir]);
    $respons = $this->actingAs(buatUser(Role::Instansi))->put(route('instansi.verifikasi.update'), $data);
    if ($diterima) {
        $respons->assertSessionHasNoErrors();
    } else {
        $respons->assertSessionHasErrors('tanggal_lahir');
    }

    $respons = $this->actingAs(buatUser(Role::AdminSurvey))->post(route('admin.survei-instansi.store'), $data + ['catatan_survei' => 'Usaha telah dikunjungi']);
    if ($diterima) {
        $respons->assertSessionHasNoErrors();
    } else {
        $respons->assertSessionHasErrors('tanggal_lahir');
    }
    expect(Instansi::count())->toBe($diterima ? 2 : 0);
})->with([
    'tepat 18 tahun' => ['2008-11-23', true],
    'lebih dari 18 tahun' => ['1990-01-01', true],
    'kurang satu hari' => ['2008-11-24', false],
    'tanggal kosong' => [null, false],
    'tanggal salah' => ['2026-02-30', false],
]);

it('admin lapangan tidak bisa menginput instansi jemput bola', function () {
    $this->actingAs(buatUser(Role::AdminLapangan))->post(route('admin.survei-instansi.store'), dataVerifikasi(['catatan_survei' => 'x']))
        ->assertForbidden();
    expect(Instansi::count())->toBe(0);
});
