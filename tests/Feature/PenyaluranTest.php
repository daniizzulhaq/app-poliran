<?php

use App\Enums\Role;
use App\Enums\StatusLowongan;
use App\Enums\StatusPendaftar;
use App\Enums\StatusPenyaluran;
use App\Models\JobListing;
use App\Models\PencariKerja;
use App\Models\Penyaluran;

beforeEach(function () {
    $this->kecA = buatKecamatan('Kec A');
    $this->kecB = buatKecamatan('Kec B');
    $this->admin = buatUser(Role::AdminLapangan, [$this->kecA->id]);
    $this->lowongan = JobListing::factory()->aktif()->create(['kuota' => 2]);
});

function salurkan($test, PencariKerja $p): Penyaluran
{
    $test->post(route('admin.penyaluran.store', $test->lowongan), ['pencari_kerja_id' => $p->id])->assertSessionHasNoErrors();

    return Penyaluran::where('pencari_kerja_id', $p->id)->firstOrFail();
}

it('admin lapangan menyalurkan pendaftar, status diproses', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);

    $penyaluran = salurkan($this, $p);
    expect($penyaluran->status)->toBe(StatusPenyaluran::Diproses);
});

it('penyaluran diterima: status disalurkan, tersalur +1, lowongan terpenuhi saat kuota penuh', function () {
    $p1 = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $p2 = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);

    $a = salurkan($this, $p1);
    $b = salurkan($this, $p2);

    $this->put(route('admin.penyaluran.hasil', $a), ['status' => 'diterima'])->assertSessionHasNoErrors();
    expect($p1->fresh()->status)->toBe(StatusPendaftar::Disalurkan)
        ->and($this->lowongan->fresh()->jumlah_tersalur)->toBe(1)
        ->and($this->lowongan->fresh()->status)->toBe(StatusLowongan::Aktif)
        ->and($p1->riwayatStatus()->first()->status_baru)->toBe(StatusPendaftar::Disalurkan);

    $this->put(route('admin.penyaluran.hasil', $b), ['status' => 'diterima'])->assertSessionHasNoErrors();
    expect($this->lowongan->fresh()->jumlah_tersalur)->toBe(2)
        ->and($this->lowongan->fresh()->status)->toBe(StatusLowongan::Terpenuhi);
});

it('tidak diterima: pendaftar bisa disalurkan ke lowongan lain', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);

    $a = salurkan($this, $p);
    $this->put(route('admin.penyaluran.hasil', $a), ['status' => 'tidak_diterima', 'catatan' => 'Tidak hadir'])->assertSessionHasNoErrors();

    expect($p->fresh()->status)->toBe(StatusPendaftar::Terdaftar)
        ->and($this->lowongan->fresh()->jumlah_tersalur)->toBe(0);

    $lain = JobListing::factory()->aktif()->create();
    $this->post(route('admin.penyaluran.store', $lain), ['pencari_kerja_id' => $p->id])->assertSessionHasNoErrors();
    expect(Penyaluran::where('job_listing_id', $lain->id)->count())->toBe(1);
});

it('pendaftar disalurkan tidak bisa disalurkan lagi', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create(['status' => StatusPendaftar::Disalurkan]);
    $this->actingAs($this->admin)
        ->post(route('admin.penyaluran.store', $this->lowongan), ['pencari_kerja_id' => $p->id])
        ->assertSessionHasErrors('pencari_kerja_id');
});

it('admin lapangan tidak bisa menyalurkan atau mencatat hasil pendaftar kecamatan lain', function () {
    $pB = PencariKerja::factory()->diKecamatan($this->kecB)->create();
    $this->actingAs($this->admin)
        ->post(route('admin.penyaluran.store', $this->lowongan), ['pencari_kerja_id' => $pB->id])->assertForbidden();

    $penyaluran = Penyaluran::create(['job_listing_id' => $this->lowongan->id, 'pencari_kerja_id' => $pB->id, 'status' => 'diproses']);
    $this->put(route('admin.penyaluran.hasil', $penyaluran), ['status' => 'diterima'])->assertForbidden();
});

it('tidak bisa menyalurkan ke lowongan yang kuotanya penuh', function () {
    $this->lowongan->update(['jumlah_tersalur' => 2]);
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin)
        ->post(route('admin.penyaluran.store', $this->lowongan), ['pencari_kerja_id' => $p->id])->assertForbidden();
});

it('instansi melihat nama pekerja yang disalurkan kepadanya', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create(['nama' => 'Pekerja Tersalur']);
    $this->actingAs($this->admin);
    $a = salurkan($this, $p);
    $this->put(route('admin.penyaluran.hasil', $a), ['status' => 'diterima']);

    $this->actingAs($this->lowongan->instansi->user)->get(route('instansi.lowongan.show', $this->lowongan))
        ->assertOk()->assertSee('Pekerja Tersalur');
});

it('halaman penyaluran admin hanya menampilkan pendaftar kecamatannya', function () {
    $pA = PencariKerja::factory()->diKecamatan($this->kecA)->create(['nama' => 'Penyaluran A']);
    $pB = PencariKerja::factory()->diKecamatan($this->kecB)->create(['nama' => 'Penyaluran B']);
    Penyaluran::create(['job_listing_id' => $this->lowongan->id, 'pencari_kerja_id' => $pA->id, 'status' => 'diproses']);
    Penyaluran::create(['job_listing_id' => $this->lowongan->id, 'pencari_kerja_id' => $pB->id, 'status' => 'diproses']);

    $this->actingAs($this->admin)->get(route('admin.penyaluran.index'))
        ->assertOk()->assertSee('Penyaluran A')->assertDontSee('Penyaluran B');
});
