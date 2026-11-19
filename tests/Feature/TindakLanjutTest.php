<?php

use App\Enums\Role;
use App\Enums\StatusPendaftar;
use App\Enums\StatusTindakLanjut;
use App\Models\JobListing;
use App\Models\PencariKerja;
use App\Models\Penyaluran;
use App\Services\JobMatcher;

beforeEach(function () {
    $this->kecA = buatKecamatan('Kec A');
    $this->kecB = buatKecamatan('Kec B');
    $this->admin = buatUser(Role::AdminLapangan, [$this->kecA->id]);
    $this->lowongan = JobListing::factory()->aktif()->create(['kuota' => 5]);
});

/** Pendaftar yang melewati alur penyaluran sampai disalurkan, mulai kerja N bulan lalu. */
function pekerjaTersalur($test, int $bulanLalu, string $statusSebelum = 'terdaftar', $kecamatan = null): Penyaluran
{
    $p = PencariKerja::factory()->diKecamatan($kecamatan ?? $test->kecA)->create(['status' => $statusSebelum]);
    $test->actingAs($test->admin);
    $test->post(route('admin.penyaluran.store', $test->lowongan), ['pencari_kerja_id' => $p->id])->assertSessionHasNoErrors();
    $penyaluran = Penyaluran::where('pencari_kerja_id', $p->id)->firstOrFail();
    $test->put(route('admin.penyaluran.tahap', $penyaluran), [
        'status' => 'menyalurkan', 'tanggal_mulai_kerja' => today()->subMonths($bulanLalu)->toDateString(), 'gaji' => '2.500.000',
    ])->assertSessionHasNoErrors();

    return $penyaluran->fresh();
}

it('menyalurkan menyimpan tanggal mulai kerja dan gaji', function () {
    $penyaluran = pekerjaTersalur($this, 0);

    expect($penyaluran->tanggal_mulai_kerja->isToday())->toBeTrue()
        ->and($penyaluran->gaji)->toBe(2500000);
});

it('menyalurkan wajib mengisi tanggal mulai kerja', function () {
    $p = PencariKerja::factory()->diKecamatan($this->kecA)->create();
    $this->actingAs($this->admin)->post(route('admin.penyaluran.store', $this->lowongan), ['pencari_kerja_id' => $p->id]);
    $penyaluran = Penyaluran::firstOrFail();

    $this->put(route('admin.penyaluran.tahap', $penyaluran), ['status' => 'menyalurkan'])->assertSessionHasErrors('tanggal_mulai_kerja');
});

it('tindak lanjut muncul saat jatuh tempo dan tercatat berurutan bulan 1, 3, 6', function () {
    $baru = pekerjaTersalur($this, 0);
    $lama = pekerjaTersalur($this, 7);

    expect(Penyaluran::tindakLanjutJatuhTempo()->pluck('id')->all())->toBe([$lama->id]);
    $this->get(route('admin.penyaluran.tindak-lanjut'))->assertOk()
        ->assertSee($lama->pencariKerja->nama)->assertDontSee($baru->pencariKerja->nama);

    foreach ([1, 3, 6] as $bulan) {
        $this->post(route('admin.penyaluran.tindak-lanjut.store', $lama), ['status' => 'masih_bekerja'])->assertSessionHasNoErrors();
        expect($lama->tindakLanjut()->max('bulan_ke'))->toBe($bulan);
    }

    expect(Penyaluran::tindakLanjutJatuhTempo()->count())->toBe(0);
    $this->post(route('admin.penyaluran.tindak-lanjut.store', $lama), ['status' => 'masih_bekerja'])->assertSessionHasErrors('status');
});

it('tindak lanjut belum jatuh tempo ditolak', function () {
    $penyaluran = pekerjaTersalur($this, 0);

    $this->post(route('admin.penyaluran.tindak-lanjut.store', $penyaluran), ['status' => 'masih_bekerja'])->assertSessionHasErrors('status');
    expect($penyaluran->tindakLanjut()->count())->toBe(0);
});

it('berhenti bekerja: alasan wajib, pendaftar kembali ke status sebelum disalurkan dan ikut pencocokan lagi', function () {
    $penyaluran = pekerjaTersalur($this, 2, 'bersertifikat');
    $pencari = $penyaluran->pencariKerja;
    expect($pencari->fresh()->status)->toBe(StatusPendaftar::Disalurkan);

    $this->post(route('admin.penyaluran.tindak-lanjut.store', $penyaluran), ['status' => 'berhenti'])->assertSessionHasErrors('catatan');
    $this->post(route('admin.penyaluran.tindak-lanjut.store', $penyaluran), ['status' => 'berhenti', 'catatan' => 'Kontrak habis'])
        ->assertSessionHasNoErrors();

    expect($pencari->fresh()->status)->toBe(StatusPendaftar::Bersertifikat)
        ->and($penyaluran->tindakLanjut()->first()->status)->toBe(StatusTindakLanjut::Berhenti)
        ->and(Penyaluran::tindakLanjutJatuhTempo()->count())->toBe(0);

    $lain = JobListing::factory()->aktif()->create();
    expect(app(JobMatcher::class)->skor($lain, $pencari->fresh()))->not->toBeNull();
});

it('pekerja yang dulu hanya direkrut kembali terdaftar setelah berhenti', function () {
    $penyaluran = pekerjaTersalur($this, 1);

    $this->post(route('admin.penyaluran.tindak-lanjut.store', $penyaluran), ['status' => 'berhenti', 'catatan' => 'Pindah'])->assertSessionHasNoErrors();
    expect($penyaluran->pencariKerja->fresh()->status)->toBe(StatusPendaftar::Terdaftar);
});

it('admin lapangan tidak bisa mencatat tindak lanjut pekerja kecamatan lain', function () {
    $this->admin = buatUser(Role::SuperAdmin);
    $penyaluran = pekerjaTersalur($this, 2, 'terdaftar', $this->kecB);

    $this->actingAs(buatUser(Role::AdminLapangan, [$this->kecA->id]))
        ->post(route('admin.penyaluran.tindak-lanjut.store', $penyaluran), ['status' => 'masih_bekerja'])->assertForbidden();
    $this->get(route('admin.penyaluran.tindak-lanjut'))->assertOk()->assertDontSee($penyaluran->pencariKerja->nama);
});

it('laporan menampilkan persentase pekerja yang bertahan', function () {
    $a = pekerjaTersalur($this, 2);
    $b = pekerjaTersalur($this, 2);
    $c = pekerjaTersalur($this, 2);
    $this->post(route('admin.penyaluran.tindak-lanjut.store', $a), ['status' => 'masih_bekerja']);
    $this->post(route('admin.penyaluran.tindak-lanjut.store', $b), ['status' => 'masih_bekerja']);
    $this->post(route('admin.penyaluran.tindak-lanjut.store', $c), ['status' => 'berhenti', 'catatan' => 'Gaji tidak dibayar']);

    $this->actingAs(buatUser(Role::SuperAdmin))->get(route('admin.laporan.index'))
        ->assertOk()->assertSee('Bertahan Bekerja')->assertSee('67%');
    $this->get(route('admin.laporan.pdf'))->assertOk();
    $this->get(route('admin.dashboard'))->assertOk()->assertSee('Tindak lanjut kerja jatuh tempo');
});

it('tautan WA berisi pesan siap kirim', function () {
    $penyaluran = pekerjaTersalur($this, 2);

    $this->get(route('admin.penyaluran.tindak-lanjut'))
        ->assertSee('wa.me/6281234567890?text=', false)
        ->assertSee(rawurlencode('Apakah masih bekerja di sana?'), false);
    $this->get(route('admin.penyaluran.index', ['status' => 'menyalurkan']))
        ->assertSee(rawurlencode('Bapak/Ibu diterima bekerja sebagai '.$this->lowongan->judul), false);
});
