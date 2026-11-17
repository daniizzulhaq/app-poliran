<?php

use App\Enums\Role;
use App\Enums\StatusPendaftar;
use App\Models\JobListing;
use App\Models\Kategori;
use App\Models\PencariKerja;
use App\Models\Skill;
use App\Services\JobMatcher;

beforeEach(function () {
    $this->kec = buatKecamatan('Kec A');
    $this->bangunan = Kategori::create(['nama' => 'Bangunan']);
    $this->pertanian = Kategori::create(['nama' => 'Pertanian']);
    $this->s1 = Skill::create(['nama' => 'Tukang Batu']);
    $this->s2 = Skill::create(['nama' => 'Tukang Kayu']);
    $this->s3 = Skill::create(['nama' => 'Pengelasan']);
    $this->matcher = app(JobMatcher::class);
});

function pencari(array $atribut, array $kategori = [], array $skills = []): PencariKerja
{
    $p = PencariKerja::factory()->diKecamatan(test()->kec)->create($atribut);
    $p->kategori()->sync($kategori);
    $p->skills()->sync($skills);

    return $p->fresh();
}

function lowongan(array $atribut = [], array $skills = []): JobListing
{
    $l = JobListing::factory()->aktif()->create(array_merge(['kategori_id' => test()->bangunan->id], $atribut));
    $l->skills()->sync($skills);

    return $l->fresh();
}

it('skor sempurna 100 bila semua cocok', function () {
    $l = lowongan(['pendidikan_min' => 'smp', 'umur_min' => 18, 'umur_max' => 40], [$this->s1->id, $this->s2->id]);
    $p = pencari(['pendidikan' => 'sma', 'tanggal_lahir' => now()->subYears(25)], [$this->bangunan->id], [$this->s1->id, $this->s2->id]);

    expect($this->matcher->skor($l, $p))->toMatchArray([
        'skor' => 100, 'kategori' => true, 'skill_sama' => 2, 'skill_diminta' => 2, 'pendidikan' => true, 'umur' => true,
    ]);
});

it('skill dihitung proporsional: 2 dari 3 skill = 27 poin', function () {
    $l = lowongan([], [$this->s1->id, $this->s2->id, $this->s3->id]);
    $p = pencari([], [$this->bangunan->id], [$this->s1->id, $this->s2->id]);

    // 40 + 40*2/3 (26.67) + 10 + 10 = 86.67 -> 87
    $r = $this->matcher->skor($l, $p);
    expect($r['skor'])->toBe(87)->and($r['skill_sama'])->toBe(2)->and($r['skill_diminta'])->toBe(3);
});

it('kategori beda, pendidikan kurang, umur di luar rentang', function () {
    $l = lowongan(['pendidikan_min' => 's1', 'umur_min' => 30, 'umur_max' => 35], [$this->s1->id]);
    $p = pencari(['pendidikan' => 'sma', 'tanggal_lahir' => now()->subYears(20)], [$this->pertanian->id], [$this->s1->id]);

    expect($this->matcher->skor($l, $p))->toMatchArray([
        'skor' => 40, 'kategori' => false, 'pendidikan' => false, 'umur' => false,
    ]);
});

it('lowongan tanpa syarat skill, pendidikan, umur memberi poin penuh pada bagian itu', function () {
    $l = lowongan();
    $p = pencari([], [$this->pertanian->id]);

    expect($this->matcher->skor($l, $p)['skor'])->toBe(60);
});

it('filter jenis kelamin dan pendaftar disalurkan', function () {
    $l = lowongan(['jenis_kelamin' => 'P']);
    $laki = pencari(['jenis_kelamin' => 'L'], [$this->bangunan->id]);
    $perempuan = pencari(['jenis_kelamin' => 'P'], [$this->bangunan->id]);
    $sudah = pencari(['jenis_kelamin' => 'P', 'status' => StatusPendaftar::Disalurkan], [$this->bangunan->id]);

    expect($this->matcher->skor($l, $laki))->toBeNull()
        ->and($this->matcher->skor($l, $sudah))->toBeNull();

    $hasil = $this->matcher->pendaftarCocok($l, buatUser(Role::SuperAdmin));
    expect($hasil->pluck('pencari.id')->all())->toBe([$perempuan->id]);
});

it('pendaftar cocok diurutkan dari skor tertinggi dan dibatasi kecamatan admin', function () {
    $kecB = buatKecamatan('Kec B');
    $l = lowongan([], [$this->s1->id, $this->s2->id]);
    $rendah = pencari([], [$this->bangunan->id]);
    $tinggi = pencari([], [$this->bangunan->id], [$this->s1->id, $this->s2->id]);
    $lain = PencariKerja::factory()->diKecamatan($kecB)->create();
    $lain->kategori()->sync([$this->bangunan->id]);

    $adminA = buatUser(Role::AdminLapangan, [$this->kec->id]);
    $hasil = $this->matcher->pendaftarCocok($l, $adminA);

    expect($hasil->pluck('pencari.id')->all())->toBe([$tinggi->id, $rendah->id]);
    expect($this->matcher->pendaftarCocok($l, buatUser(Role::SuperAdmin)))->toHaveCount(3);
});

it('tab pendaftar cocok tampil di detail lowongan dan panel lowongan cocok di detail pendaftar', function () {
    $l = lowongan(['judul' => 'Tukang Bangunan Gedung'], [$this->s1->id]);
    $p = pencari(['nama' => 'Yakobus Cocok'], [$this->bangunan->id], [$this->s1->id]);
    $admin = buatUser(Role::AdminLapangan, [$this->kec->id]);

    $this->actingAs($admin)->get(route('admin.lowongan.show', $l))
        ->assertOk()->assertSee('Pendaftar Cocok')->assertSee('Yakobus Cocok')->assertSee('skill 1/1');

    $this->get(route('admin.pendaftar.show', $p))
        ->assertOk()->assertSee('Lowongan Cocok')->assertSee('Tukang Bangunan Gedung');
});

it('instansi tidak melihat hasil pencocokan', function () {
    $l = lowongan();
    pencari(['nama' => 'Rahasia Pencocokan'], [$this->bangunan->id]);

    $this->actingAs($l->instansi->user)->get(route('instansi.lowongan.show', $l))
        ->assertOk()->assertDontSee('Rahasia Pencocokan')->assertDontSee('Pendaftar Cocok');
});
