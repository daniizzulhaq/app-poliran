<?php

use App\Enums\Role;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Services\WilayahImporter;
use Database\Seeders\WilayahSeeder;
use Illuminate\Support\Facades\Http;

function fakeApiWilayah(): void
{
    Http::fake([
        'wilayah.id/api/districts/53.02.json' => Http::response(['data' => [
            ['code' => '53.02.01', 'name' => 'Kota Soe'],
            ['code' => '53.02.10', 'name' => "KI'E"],
        ]]),
        'wilayah.id/api/villages/53.02.01.json' => Http::response(['data' => [
            ['code' => '53.02.01.1001', 'name' => 'SoE'],
            ['code' => '53.02.01.1002', 'name' => 'Cendana'],
        ]]),
        'wilayah.id/api/villages/53.02.10.json' => Http::response(['data' => [
            ['code' => '53.02.10.2001', 'name' => 'BOKONG'],
        ]]),
    ]);
}

it('impor wilayah TTS dari API: nama dirapikan, kode tersimpan, tidak dobel saat diulang', function () {
    fakeApiWilayah();

    $this->artisan('wilayah:impor')->assertSuccessful();
    expect(Kecamatan::orderBy('kode')->pluck('nama', 'kode')->all())->toBe(['53.02.01' => 'Kota Soe', '53.02.10' => "Ki'e"])
        ->and(Desa::where('kode', '53.02.01.1001')->value('nama'))->toBe('Soe')
        ->and(Desa::count())->toBe(3);

    $this->artisan('wilayah:impor')->assertSuccessful();
    expect(Kecamatan::count())->toBe(2)->and(Desa::count())->toBe(3);
});

it('impor tidak menimpa nama yang sudah diubah admin dan melengkapi kode data lama', function () {
    $lama = Kecamatan::create(['nama' => 'kota soe']);
    $diubah = Kecamatan::create(['nama' => "Ki'e (ubahan admin)", 'kode' => '53.02.10']);
    fakeApiWilayah();

    app(WilayahImporter::class)->simpan(app(WilayahImporter::class)->dariApi());

    expect($lama->fresh()->kode)->toBe('53.02.01')
        ->and($diubah->fresh()->nama)->toBe("Ki'e (ubahan admin)")
        ->and(Kecamatan::count())->toBe(2);
});

it('super admin bisa sinkron dari halaman kecamatan; gagal koneksi tampil pesan', function () {
    $this->actingAs(buatUser(Role::SuperAdmin));

    fakeApiWilayah();
    $this->post(route('admin.kecamatan.sinkronkan'))->assertSessionHas('success');
    expect(Kecamatan::count())->toBe(2);
});

it('sinkron wilayah menampilkan pesan bila server wilayah tidak bisa dihubungi', function () {
    Http::fake(['wilayah.id/*' => Http::response('', 500)]);
    $this->actingAs(buatUser(Role::SuperAdmin))
        ->post(route('admin.kecamatan.sinkronkan'))->assertSessionHas('error');
});

it('hanya super admin yang bisa sinkron wilayah', function () {
    $this->actingAs(buatUser(Role::AdminLapangan))->post(route('admin.kecamatan.sinkronkan'))->assertForbidden();
});

it('seeder memuat 32 kecamatan TTS dari salinan CSV', function () {
    $this->seed(WilayahSeeder::class);

    expect(Kecamatan::count())->toBe(32)
        ->and(Kecamatan::whereNull('kode')->count())->toBe(0)
        ->and(Kecamatan::where('nama', 'Kota Soe')->first()->desa()->count())->toBeGreaterThan(0)
        ->and(Desa::count())->toBeGreaterThan(250);
});
