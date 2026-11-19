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

function tahap($test, Penyaluran $penyaluran, string $status, array $data = [])
{
    if ($status === 'menyalurkan') {
        $data += ['tanggal_mulai_kerja' => today()->toDateString()];
    }

    return $test->put(route('admin.penyaluran.tahap', $penyaluran), ['status' => $status] + $data);
}

it('admin lapangan merekrut pendaftar: tahap merekrut, pendaftar direkrut', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);

    $penyaluran = salurkan($this, $p);
    expect($penyaluran->status)->toBe(StatusPenyaluran::Merekrut)
        ->and($p->fresh()->status)->toBe(StatusPendaftar::Direkrut);
});

it('alur lengkap merekrut -> melatih bersertifikat -> menyalurkan mengikuti status pendaftar', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);
    $a = salurkan($this, $p);

    tahap($this, $a, 'bersertifikat')->assertSessionHasNoErrors();
    expect($a->fresh()->status)->toBe(StatusPenyaluran::Bersertifikat)
        ->and($a->fresh()->hasil_pada)->toBeNull()
        ->and($p->fresh()->status)->toBe(StatusPendaftar::Bersertifikat);

    tahap($this, $a, 'menyalurkan')->assertSessionHasNoErrors();
    expect($a->fresh()->status)->toBe(StatusPenyaluran::Menyalurkan)
        ->and($a->fresh()->hasil_pada)->not->toBeNull()
        ->and($p->fresh()->status)->toBe(StatusPendaftar::Disalurkan)
        ->and($this->lowongan->fresh()->jumlah_tersalur)->toBe(1)
        ->and($p->riwayatStatus()->reorder('id')->pluck('status_baru')->all())
        ->toBe([StatusPendaftar::Direkrut, StatusPendaftar::Bersertifikat, StatusPendaftar::Disalurkan]);
});

it('melatih non bersertifikat menjadikan pendaftar non bersertifikat', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);
    $a = salurkan($this, $p);

    tahap($this, $a, 'non_bersertifikat')->assertSessionHasNoErrors();
    expect($p->fresh()->status)->toBe(StatusPendaftar::NonBersertifikat);
});

it('pelatihan boleh dilewati; lowongan terpenuhi saat kuota penuh', function () {
    $p1 = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $p2 = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);
    $a = salurkan($this, $p1);
    $b = salurkan($this, $p2);

    tahap($this, $a, 'menyalurkan')->assertSessionHasNoErrors();
    expect($p1->fresh()->status)->toBe(StatusPendaftar::Disalurkan)
        ->and($this->lowongan->fresh()->status)->toBe(StatusLowongan::Aktif);

    tahap($this, $b, 'menyalurkan')->assertSessionHasNoErrors();
    expect($this->lowongan->fresh()->jumlah_tersalur)->toBe(2)
        ->and($this->lowongan->fresh()->status)->toBe(StatusLowongan::Terpenuhi);
});

it('tahap yang tidak berurutan ditolak', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);
    $a = salurkan($this, $p);

    tahap($this, $a, 'merekrut')->assertSessionHasErrors('status');
    tahap($this, $a, 'non_bersertifikat')->assertSessionHasNoErrors();
    tahap($this, $a, 'bersertifikat')->assertSessionHasErrors('status');
    expect($a->fresh()->status)->toBe(StatusPenyaluran::NonBersertifikat);
});

it('penyaluran yang sudah selesai tidak bisa diubah', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);
    $a = salurkan($this, $p);
    tahap($this, $a, 'tidak_diterima')->assertSessionHasNoErrors();

    tahap($this, $a, 'menyalurkan')->assertForbidden();
});

it('tidak diterima: pendaftar kembali terdaftar dan bisa disalurkan ke lowongan lain', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);

    $a = salurkan($this, $p);
    tahap($this, $a, 'tidak_diterima', ['catatan' => 'Tidak hadir'])->assertSessionHasNoErrors();

    expect($p->fresh()->status)->toBe(StatusPendaftar::Terdaftar)
        ->and($a->fresh()->catatan)->toBe('Tidak hadir')
        ->and($this->lowongan->fresh()->jumlah_tersalur)->toBe(0);

    $lain = JobListing::factory()->aktif()->create();
    $this->post(route('admin.penyaluran.store', $lain), ['pencari_kerja_id' => $p->id])->assertSessionHasNoErrors();
    expect(Penyaluran::where('job_listing_id', $lain->id)->count())->toBe(1);
});

it('tidak diterima setelah dilatih: status pelatihan pendaftar tetap', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin);
    $a = salurkan($this, $p);
    tahap($this, $a, 'bersertifikat');
    tahap($this, $a, 'tidak_diterima')->assertSessionHasNoErrors();

    expect($p->fresh()->status)->toBe(StatusPendaftar::Bersertifikat);
});

it('status pendaftar tidak diturunkan saat direkrut', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create(['status' => StatusPendaftar::Bersertifikat]);
    $this->actingAs($this->admin);

    salurkan($this, $p);
    expect($p->fresh()->status)->toBe(StatusPendaftar::Bersertifikat);
});

it('pendaftar yang sudah disalurkan ke lowongan lain tidak bisa disalurkan lagi', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $lain = JobListing::factory()->aktif()->create();
    $this->actingAs($this->admin);
    $a = salurkan($this, $p);
    $this->post(route('admin.penyaluran.store', $lain), ['pencari_kerja_id' => $p->id])->assertSessionHasNoErrors();
    $b = Penyaluran::where('job_listing_id', $lain->id)->firstOrFail();

    tahap($this, $b, 'menyalurkan')->assertSessionHasNoErrors();
    tahap($this, $a, 'menyalurkan')->assertSessionHasErrors('status');
    expect($this->lowongan->fresh()->jumlah_tersalur)->toBe(0);
});

it('pendaftar disalurkan tidak bisa direkrut lagi', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create(['status' => StatusPendaftar::Disalurkan]);
    $this->actingAs($this->admin)
        ->post(route('admin.penyaluran.store', $this->lowongan), ['pencari_kerja_id' => $p->id])
        ->assertSessionHasErrors('pencari_kerja_id');
});

it('admin lapangan tidak bisa merekrut atau mengubah tahap pendaftar kecamatan lain', function () {
    $pB = PencariKerja::factory()->diKecamatan($this->kecB)->create();
    $this->actingAs($this->admin)
        ->post(route('admin.penyaluran.store', $this->lowongan), ['pencari_kerja_id' => $pB->id])->assertForbidden();

    $penyaluran = Penyaluran::create(['job_listing_id' => $this->lowongan->id, 'pencari_kerja_id' => $pB->id, 'status' => 'merekrut']);
    tahap($this, $penyaluran, 'menyalurkan')->assertForbidden();
});

it('tidak bisa merekrut ke lowongan yang kuotanya penuh', function () {
    $this->lowongan->update(['jumlah_tersalur' => 2]);
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin)
        ->post(route('admin.penyaluran.store', $this->lowongan), ['pencari_kerja_id' => $p->id])->assertForbidden();
});

it('instansi melihat nama pekerja yang disalurkan kepadanya', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create(['nama' => 'Pekerja Tersalur']);
    $this->actingAs($this->admin);
    $a = salurkan($this, $p);
    tahap($this, $a, 'menyalurkan');

    $this->actingAs($this->lowongan->instansi->user)->get(route('instansi.lowongan.show', $this->lowongan))
        ->assertOk()->assertSee('Pekerja Tersalur');
});

it('halaman penyaluran admin per tahap dan hanya menampilkan pendaftar kecamatannya', function () {
    $pA = PencariKerja::factory()->diKecamatan($this->kecA)->create(['nama' => 'Penyaluran A']);
    $pB = PencariKerja::factory()->diKecamatan($this->kecB)->create(['nama' => 'Penyaluran B']);
    $pC = PencariKerja::factory()->diKecamatan($this->kecA)->create(['nama' => 'Penyaluran C']);
    Penyaluran::create(['job_listing_id' => $this->lowongan->id, 'pencari_kerja_id' => $pA->id, 'status' => 'merekrut']);
    Penyaluran::create(['job_listing_id' => $this->lowongan->id, 'pencari_kerja_id' => $pB->id, 'status' => 'merekrut']);
    Penyaluran::create(['job_listing_id' => $this->lowongan->id, 'pencari_kerja_id' => $pC->id, 'status' => 'bersertifikat']);

    $this->actingAs($this->admin)->get(route('admin.penyaluran.index'))
        ->assertOk()->assertSee('Penyaluran A')->assertDontSee('Penyaluran B')->assertDontSee('Penyaluran C')
        ->assertSee('Melatih - bersertifikat (lembaga formal)');

    $this->get(route('admin.penyaluran.index', ['status' => 'bersertifikat']))
        ->assertOk()->assertSee('Penyaluran C')->assertDontSee('Penyaluran A')
        ->assertSee('Menyalurkan (diterima kerja)')->assertDontSee('Melatih - non bersertifikat');
});
