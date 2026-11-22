<?php

use App\Enums\Role;
use App\Enums\StatusLowongan;
use App\Enums\WilayahKerja;
use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\Kategori;
use App\Models\Skill;

function dataLowongan(array $ubah = []): array
{
    return array_merge([
        'judul' => 'Tukang Batu', 'kategori_id' => Kategori::firstOrCreate(['nama' => 'Bangunan'])->id,
        'skills' => [Skill::firstOrCreate(['nama' => 'Tukang Batu'])->id], 'jobdesk' => 'Membangun rumah',
        'gaji_min' => '2500000', 'gaji_max' => '3000000', 'tipe_kerja' => 'harian', 'lokasi_kerja' => 'SoE', 'wilayah_kerja' => 'dalam_tts',
        'kuota' => 3, 'batas_tanggal' => now()->addWeek()->toDateString(),
    ], $ubah);
}

it('instansi belum terverifikasi tidak bisa upload lowongan', function () {
    $instansi = Instansi::factory()->create();
    $this->actingAs($instansi->user);

    $this->get(route('instansi.lowongan.create'))->assertForbidden();
    $this->post(route('instansi.lowongan.store'), dataLowongan())->assertForbidden();
    expect(JobListing::count())->toBe(0);
});

it('instansi tanpa data verifikasi tidak bisa upload lowongan', function () {
    $this->actingAs(buatUser(Role::Instansi))->post(route('instansi.lowongan.store'), dataLowongan())->assertForbidden();
});

it('instansi terverifikasi bisa upload lowongan dan statusnya menunggu survei', function () {
    $instansi = Instansi::factory()->terverifikasi()->create();
    $this->actingAs($instansi->user)->post(route('instansi.lowongan.store'), dataLowongan())->assertRedirect();

    $lowongan = JobListing::first();
    expect($lowongan->status)->toBe(StatusLowongan::MenungguSurvey)
        ->and($lowongan->instansi_id)->toBe($instansi->id)
        ->and($lowongan->skills)->toHaveCount(1);
});

it('gaji wajib diisi kecuali nego', function () {
    $instansi = Instansi::factory()->terverifikasi()->create();
    $this->actingAs($instansi->user);
    $this->post(route('instansi.lowongan.store'), dataLowongan(['gaji_min' => '', 'gaji_max' => '']))->assertSessionHasErrors('gaji_min');
    $this->post(route('instansi.lowongan.store'), dataLowongan(['gaji_min' => '', 'gaji_max' => '', 'gaji_nego' => '1']))->assertSessionHasNoErrors();
});

it('instansi tidak bisa melihat lowongan instansi lain', function () {
    $milikLain = JobListing::factory()->aktif()->create(['judul' => 'Lowongan Milik Lain']);
    $instansi = Instansi::factory()->terverifikasi()->create();
    JobListing::factory()->create(['instansi_id' => $instansi->id, 'judul' => 'Lowongan Saya Sendiri']);

    $this->actingAs($instansi->user);
    $this->get(route('instansi.lowongan.show', $milikLain))->assertForbidden();
    $this->get(route('instansi.lowongan.edit', $milikLain))->assertForbidden();
    $this->post(route('instansi.lowongan.tutup', $milikLain))->assertForbidden();
    $this->get(route('instansi.lowongan.index'))->assertSee('Lowongan Saya Sendiri')->assertDontSee('Lowongan Milik Lain');
});

it('pencari kerja dan tamu hanya bisa membuka lowongan melalui halaman publik', function () {
    $lowongan = JobListing::factory()->aktif()->create(['judul' => 'Lowongan Rahasia']);
    $urls = [
        route('admin.lowongan.index'), route('admin.lowongan.show', $lowongan),
        route('instansi.lowongan.index'), route('instansi.lowongan.show', $lowongan),
        route('admin.survei-lowongan.index'),
    ];

    foreach ($urls as $url) {
        $this->get($url)->assertRedirect(route('login'));
    }

    $this->actingAs(buatUser(Role::PencariKerja));
    foreach ($urls as $url) {
        $this->get($url)->assertForbidden();
    }

    auth()->logout();
    $this->get(route('home'))->assertOk()->assertSee('Lowongan Rahasia');
    $this->get(route('public.pelatihan.index'))->assertOk()->assertDontSee('Lowongan Rahasia');
});

it('admin survei bisa ACC dan menolak lowongan dengan alasan', function () {
    $a = JobListing::factory()->create();
    $b = JobListing::factory()->create();
    $this->actingAs(buatUser(Role::AdminSurvey));

    $this->get(route('admin.lowongan.show', $a))->assertOk()->assertSee('Hasil Survei Lowongan');
    $this->post(route('admin.survei-lowongan.putusan', $a), ['keputusan' => 'terima'])->assertRedirect();
    $this->post(route('admin.survei-lowongan.putusan', $b), ['keputusan' => 'tolak'])->assertSessionHasErrors('alasan_tolak');
    $this->post(route('admin.survei-lowongan.putusan', $b), ['keputusan' => 'tolak', 'alasan_tolak' => 'Meminta uang']);

    expect($a->fresh()->status)->toBe(StatusLowongan::Aktif)
        ->and($b->fresh()->status)->toBe(StatusLowongan::Ditolak);
});

it('admin lapangan tidak bisa menyurvei lowongan', function () {
    $a = JobListing::factory()->create();
    $this->actingAs(buatUser(Role::AdminLapangan))
        ->post(route('admin.survei-lowongan.putusan', $a), ['keputusan' => 'terima'])->assertForbidden();
});

it('instansi bisa menutup lowongan sendiri', function () {
    $lowongan = JobListing::factory()->aktif()->create();
    $this->actingAs($lowongan->instansi->user)->post(route('instansi.lowongan.tutup', $lowongan))->assertRedirect();
    expect($lowongan->fresh()->status)->toBe(StatusLowongan::Ditutup);
});

it('lowongan lewat batas tanggal otomatis kedaluwarsa', function () {
    $lewat = JobListing::factory()->aktif()->create(['batas_tanggal' => now()->subDay()->toDateString()]);
    $masih = JobListing::factory()->aktif()->create();

    expect(JobListing::aktif()->pluck('id')->all())->toBe([$masih->id]);

    $this->artisan('lowongan:tutup-kedaluwarsa')->assertSuccessful();
    expect($lewat->fresh()->status)->toBe(StatusLowongan::Kedaluwarsa)
        ->and($masih->fresh()->status)->toBe(StatusLowongan::Aktif);
});

it('lowongan ditolak bisa diubah dan dikirim ulang', function () {
    $lowongan = JobListing::factory()->create(['status' => StatusLowongan::Ditolak, 'alasan_tolak' => 'x']);
    $this->actingAs($lowongan->instansi->user)
        ->put(route('instansi.lowongan.update', $lowongan), dataLowongan())->assertRedirect();
    expect($lowongan->fresh()->status)->toBe(StatusLowongan::MenungguSurvey);
});

it('lowongan luar negeri wajib mencantumkan izin P3MI', function () {
    $instansi = Instansi::factory()->terverifikasi()->create();
    $this->actingAs($instansi->user);

    $this->post(route('instansi.lowongan.store'), dataLowongan(['wilayah_kerja' => 'luar_negeri']))->assertSessionHasErrors('nomor_izin_p3mi');
    $this->post(route('instansi.lowongan.store'), dataLowongan(['wilayah_kerja' => 'luar_negeri', 'nomor_izin_p3mi' => 'SIP3MI-123']))
        ->assertSessionHasNoErrors();

    $lowongan = JobListing::firstOrFail();
    expect($lowongan->wilayah_kerja)->toBe(WilayahKerja::LuarNegeri)->and($lowongan->nomor_izin_p3mi)->toBe('SIP3MI-123');
});

it('izin P3MI dikosongkan bila lowongan bukan luar negeri', function () {
    $instansi = Instansi::factory()->terverifikasi()->create();
    $this->actingAs($instansi->user)
        ->post(route('instansi.lowongan.store'), dataLowongan(['wilayah_kerja' => 'luar_tts', 'nomor_izin_p3mi' => 'ABC']))
        ->assertSessionHasNoErrors();

    expect(JobListing::firstOrFail()->nomor_izin_p3mi)->toBeNull();
});

it('lowongan luar TTS hanya bisa di-ACC setelah legalitas diperiksa', function () {
    $lowongan = JobListing::factory()->create(['wilayah_kerja' => 'luar_tts']);
    $this->actingAs(buatUser(Role::AdminSurvey));

    $this->get(route('admin.lowongan.show', $lowongan))->assertOk()->assertSee('rawan calo/TPPO')->assertSee('Biaya perjalanan');
    $this->post(route('admin.survei-lowongan.putusan', $lowongan), ['keputusan' => 'terima'])->assertSessionHasErrors('legalitas_diperiksa');
    expect($lowongan->fresh()->status)->toBe(StatusLowongan::MenungguSurvey);

    // Menolak tidak butuh centang legalitas.
    $tolak = JobListing::factory()->create(['wilayah_kerja' => 'luar_negeri']);
    $this->post(route('admin.survei-lowongan.putusan', $tolak), ['keputusan' => 'tolak', 'alasan_tolak' => 'Izin tidak sah'])->assertSessionHasNoErrors();

    $this->post(route('admin.survei-lowongan.putusan', $lowongan), ['keputusan' => 'terima', 'legalitas_diperiksa' => '1'])->assertSessionHasNoErrors();
    expect($lowongan->fresh()->status)->toBe(StatusLowongan::Aktif)
        ->and($lowongan->fresh()->legalitas_diperiksa_pada)->not->toBeNull();
});

it('petugas survei bisa menginput lowongan untuk instansi terverifikasi dan langsung aktif', function () {
    $instansi = Instansi::factory()->terverifikasi()->create();
    $belum = Instansi::factory()->create();
    $petugas = buatUser(Role::AdminSurvey);
    $this->actingAs($petugas);

    $this->get(route('admin.lowongan.create', ['instansi_id' => $instansi->id]))->assertOk()->assertSee($instansi->nama)->assertDontSee($belum->nama);
    $this->post(route('admin.lowongan.store'), dataLowongan(['instansi_id' => $belum->id]))->assertSessionHasErrors('instansi_id');
    $this->post(route('admin.lowongan.store'), dataLowongan(['instansi_id' => $instansi->id, 'wilayah_kerja' => 'luar_tts']))
        ->assertSessionHasErrors('legalitas_diperiksa');

    $this->post(route('admin.lowongan.store'), dataLowongan(['instansi_id' => $instansi->id, 'catatan_survei' => 'Bertemu pemilik']))
        ->assertSessionHasNoErrors();

    $lowongan = JobListing::firstOrFail();
    expect($lowongan->status)->toBe(StatusLowongan::Aktif)
        ->and($lowongan->instansi_id)->toBe($instansi->id)
        ->and($lowongan->dibuat_oleh)->toBe($petugas->id)
        ->and($lowongan->disurvei_oleh)->toBe($petugas->id)
        ->and($lowongan->catatan_survei)->toContain('Bertemu pemilik')
        ->and($lowongan->skills)->toHaveCount(1);
});

it('admin lapangan dan instansi tidak bisa menginput lowongan sebagai petugas', function () {
    $instansi = Instansi::factory()->terverifikasi()->create();

    $this->actingAs(buatUser(Role::AdminLapangan))->get(route('admin.lowongan.create'))->assertForbidden();
    $this->actingAs($instansi->user)->post(route('admin.lowongan.store'), dataLowongan(['instansi_id' => $instansi->id]))->assertForbidden();
    expect(JobListing::count())->toBe(0);
});
