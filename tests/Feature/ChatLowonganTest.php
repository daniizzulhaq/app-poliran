<?php

use App\Enums\Role;
use App\Enums\StatusLowongan;
use App\Models\ChatMessage;
use App\Models\JobChat;
use App\Models\JobListing;
use App\Models\PencariKerja;
use App\Models\Penyaluran;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Str;

function pencariChat(): User
{
    $user = buatUser(Role::PencariKerja);
    PencariKerja::factory()->create(['user_id' => $user->id]);

    return $user;
}

function mulaiChat($test, $user, $lowongan): JobChat
{
    $test->actingAs($user)->post(route('chat.start', $lowongan))->assertRedirect();

    return JobChat::where('user_id', $user->id)->where('job_listing_id', $lowongan->id)->firstOrFail();
}

it('menampilkan hanya lowongan aktif belum kedaluwarsa dan belum penuh tanpa data internal', function () {
    $aktif = JobListing::factory()->aktif()->create([
        'judul' => 'Pekerjaan Terbuka', 'catatan_survei' => 'Catatan internal rahasia',
    ]);
    $aktif->instansi->update(['no_wa' => '089999999999']);
    $tersembunyi = [
        JobListing::factory()->create(['judul' => 'Masih Disurvei']),
        JobListing::factory()->create(['judul' => 'Sudah Ditolak', 'status' => StatusLowongan::Ditolak]),
        JobListing::factory()->aktif()->create(['judul' => 'Sudah Lewat', 'batas_tanggal' => today()->subDay()]),
        JobListing::factory()->aktif()->create(['judul' => 'Sudah Penuh', 'jumlah_tersalur' => 2, 'kuota' => 2]),
        JobListing::factory()->create(['judul' => 'Sudah Ditutup', 'status' => StatusLowongan::Ditutup]),
    ];

    foreach ([route('home'), route('public.lowongan.index')] as $url) {
        $response = $this->get($url)->assertOk()->assertSee($aktif->judul)->assertDontSee('Catatan internal rahasia')->assertDontSee('089999999999');
        foreach ($tersembunyi as $item) {
            $response->assertDontSee($item->judul);
        }
    }
    $this->get(route('public.lowongan.show', $aktif))->assertOk()->assertSee('Chat Admin')->assertDontSee('Catatan internal rahasia')->assertDontSee('089999999999');
    foreach ($tersembunyi as $item) {
        $this->get(route('public.lowongan.show', $item))->assertNotFound();
    }
    $this->actingAs(buatUser(Role::PencariKerja))->get(route('pencari.dashboard'))->assertOk()->assertSee($aktif->judul);
});

it('tamu harus masuk dan pekerja harus memiliki profil untuk memulai chat', function () {
    $lowongan = JobListing::factory()->aktif()->create();
    $this->get(route('chat.interest', $lowongan))->assertRedirect(route('login'));
    $this->post(route('chat.start', $lowongan))->assertRedirect(route('login'));
    $this->actingAs(buatUser(Role::PencariKerja))->post(route('chat.start', $lowongan))->assertRedirect(route('pencari.profil.edit'));
    expect(JobChat::count())->toBe(0);
});

it('membuat satu percakapan per pekerja dan lowongan serta menyimpan pesan minat', function () {
    $user = pencariChat();
    $lowongan = JobListing::factory()->aktif()->create();
    $chat = mulaiChat($this, $user, $lowongan);
    $this->post(route('chat.start', $lowongan))->assertRedirect(route('chat.show', $chat));
    expect(JobChat::count())->toBe(1)->and($chat->messages()->count())->toBe(1);
    $this->get(route('chat.show', $chat))->assertOk()->assertSee('Chat Admin Utama')->assertSee($lowongan->judul);
    $this->getJson(route('chat.index'))->assertOk()->assertJsonPath('chats.0.name', 'Admin Utama');
    $lain = JobListing::factory()->aktif()->create();
    mulaiChat($this, $user, $lain);
    expect(JobChat::count())->toBe(2);
});

it('hanya pemilik percakapan dan super admin yang dapat membaca dan membalas', function () {
    $user = pencariChat();
    $lowongan = JobListing::factory()->aktif()->create();
    $chat = mulaiChat($this, $user, $lowongan);

    foreach ([pencariChat(), buatUser(Role::AdminSurvey), buatUser(Role::AdminLapangan), $lowongan->instansi->user] as $lain) {
        $this->actingAs($lain)->get(route('chat.show', $chat))->assertForbidden();
        $this->getJson(route('chat.messages', $chat))->assertForbidden();
        $this->postJson(route('chat.send', $chat), ['body' => 'Tidak berhak', 'client_id' => (string) Str::uuid()])->assertForbidden();
        if (! $lain->isRole(Role::PencariKerja)) {
            $this->getJson(route('chat.index'))->assertForbidden();
            $this->getJson(route('chat.unread'))->assertForbidden();
            $this->post(route('chat.start', $lowongan))->assertForbidden();
        } else {
            $this->getJson(route('chat.index'))->assertJsonPath('chats', []);
            $this->getJson(route('chat.unread'))->assertJsonPath('count', 0);
        }
    }
    $this->actingAs(buatUser(Role::SuperAdmin))->get(route('chat.show', $chat))->assertOk();
});

it('pesan baru diambil bertahap dan penanda belum dibaca sesuai penerimanya', function () {
    $user = pencariChat();
    $chat = mulaiChat($this, $user, JobListing::factory()->aktif()->create());
    $initialId = $chat->messages()->first()->id;
    $admin = buatUser(Role::SuperAdmin);
    $this->actingAs($admin)->getJson(route('chat.unread'))->assertJsonPath('count', 1);
    $this->getJson(route('chat.index'))->assertJsonPath('chats.0.unread', 1);
    $this->getJson(route('chat.messages', $chat))->assertJsonCount(1, 'messages');
    $this->getJson(route('chat.unread'))->assertJsonPath('count', 0);

    $reply = $this->postJson(route('chat.send', $chat), [
        'body' => 'Silakan jelaskan pengalaman Anda.', 'client_id' => (string) Str::uuid(),
        'sender_id' => $user->id, 'is_admin' => false,
    ])->assertCreated()->assertJsonPath('message.mine', true)->json('message.id');
    expect(ChatMessage::find($reply)->sender_id)->toBe($admin->id)->and(ChatMessage::find($reply)->is_admin)->toBeTrue();
    $this->actingAs($user)->getJson(route('chat.unread'))->assertJsonPath('count', 1);
    $this->getJson(route('chat.messages', [$chat, 'after' => $initialId]))
        ->assertJsonCount(1, 'messages')->assertJsonPath('messages.0.id', $reply)->assertJsonPath('messages.0.mine', false);
    $this->getJson(route('chat.messages', [$chat, 'after' => $reply]))->assertJsonCount(0, 'messages');
    $this->getJson(route('chat.unread'))->assertJsonPath('count', 0);
    $this->postJson(route('chat.send', $chat), ['body' => 'Saya berpengalaman dua tahun.', 'client_id' => (string) Str::uuid()])->assertCreated();
    $this->actingAs($admin)->getJson(route('chat.unread'))->assertJsonPath('count', 1);
});

it('admin melihat kemampuan profil CV dan rincian kecocokan untuk lowongan yang dipilih', function () {
    $user = pencariChat();
    $skill = Skill::create(['nama' => 'Las Listrik']);
    $lowongan = JobListing::factory()->aktif()->create(['pendidikan_min' => 'sma', 'umur_min' => 18, 'umur_max' => 40]);
    $lowongan->skills()->attach($skill);
    $user->pencariKerja->skills()->attach($skill);
    $user->pencariKerja->kategori()->attach($lowongan->kategori_id);
    $user->pencariKerja->update(['cv_path' => 'cv/uji.pdf', 'pengalaman_kerja' => 'Dua tahun mengelas']);
    $chat = mulaiChat($this, $user, $lowongan);
    $this->actingAs(buatUser(Role::SuperAdmin))->get(route('chat.show', $chat))
        ->assertOk()->assertSee('Las Listrik')->assertSee('Dua tahun mengelas')->assertSee('Lihat CV')
        ->assertSee($user->pencariKerja->nik)
        ->assertSee('Kecocokan dengan Lowongan')->assertSee('1 dari 1 sesuai')->assertSee('100')
        ->assertSee(route('admin.pendaftar.show', $user->pencariKerja), false);
});

it('validasi pesan dan percobaan ulang pengiriman tidak menggandakan pesan', function () {
    $user = pencariChat();
    $chat = mulaiChat($this, $user, JobListing::factory()->aktif()->create());
    $payload = ['body' => 'Pesan satu kali', 'client_id' => (string) Str::uuid()];
    $id = $this->postJson(route('chat.send', $chat), $payload)->assertCreated()->json('message.id');
    $this->postJson(route('chat.send', $chat), $payload)->assertCreated()->assertJsonPath('message.id', $id);
    expect($chat->messages()->count())->toBe(2);
    foreach (['   ', str_repeat('a', 4001), ['pesan' => 'Format tidak valid']] as $body) {
        $this->postJson(route('chat.send', $chat), ['body' => $body, 'client_id' => (string) Str::uuid()])->assertUnprocessable()->assertJsonValidationErrors('body');
    }
    $this->postJson(route('chat.send', $chat), ['body' => 'Tes', 'client_id' => 'bukan-uuid'])->assertUnprocessable();
});

it('riwayat panjang dapat dimuat tanpa melewatkan pesan atau menandai pesan yang belum diambil', function () {
    $user = pencariChat();
    $chat = mulaiChat($this, $user, JobListing::factory()->aktif()->create());
    for ($i = 0; $i < 65; $i++) {
        $chat->messages()->create(['sender_id' => $user->id, 'client_id' => (string) Str::uuid(), 'is_admin' => false, 'body' => 'Pesan '.$i]);
    }
    $this->actingAs(buatUser(Role::SuperAdmin));
    $batch = $this->getJson(route('chat.messages', $chat))->assertJsonCount(50, 'messages')->assertJsonPath('has_more', true)->json('messages');
    $this->getJson(route('chat.unread'))->assertJsonPath('count', 16);
    $this->getJson(route('chat.messages', [$chat, 'after' => $batch[49]['id']]))->assertJsonCount(16, 'messages')->assertJsonPath('has_more', false);
    $this->getJson(route('chat.unread'))->assertJsonPath('count', 0);
    $this->getJson(route('chat.messages', [$chat, 'before' => $batch[49]['id']]))->assertJsonCount(49, 'messages');
    $this->getJson(route('chat.messages', [$chat, 'before' => 1, 'after' => 1]))->assertUnprocessable();
    $this->get(route('chat.show', $chat))->assertOk()->assertViewHas('hasOlder', true);
});

it('chat lama tetap dapat digunakan setelah lowongan ditutup tetapi chat baru ditolak', function () {
    $user = pencariChat();
    $lowongan = JobListing::factory()->aktif()->create();
    $chat = mulaiChat($this, $user, $lowongan);
    $lowongan->update(['status' => StatusLowongan::Ditutup]);
    $this->get(route('chat.show', $chat))->assertOk()->assertSee('Lowongan sudah ditutup');
    $this->postJson(route('chat.send', $chat), ['body' => 'Ada lowongan lain?', 'client_id' => (string) Str::uuid()])->assertCreated();
    $this->actingAs(pencariChat())->post(route('chat.start', $lowongan))->assertNotFound();
});

it('admin utama meneruskan rekrutmen dari chat ke admin lapangan sesuai domisili pekerja', function () {
    $user = pencariChat();
    $pencari = $user->pencariKerja;
    $wilayahLain = buatKecamatan('Kecamatan Tujuan Pekerjaan');
    $adminWilayah = buatUser(Role::AdminLapangan, [$pencari->kecamatan_id], ['name' => 'Petugas Domisili']);
    $adminLain = buatUser(Role::AdminLapangan, [$wilayahLain->id], ['name' => 'Petugas Wilayah Lain']);
    buatUser(Role::AdminLapangan, [$pencari->kecamatan_id], ['name' => 'Petugas Nonaktif', 'is_active' => false]);
    $lowongan = JobListing::factory()->aktif()->create(['lokasi_kerja' => $wilayahLain->nama]);
    $chat = mulaiChat($this, $user, $lowongan);
    $adminUtama = buatUser(Role::SuperAdmin);

    $this->actingAs($adminUtama)->get(route('chat.show', $chat))->assertOk()
        ->assertSee('Rekrut & Teruskan ke Admin Lapangan', false)->assertSee('Petugas Domisili')
        ->assertDontSee('Petugas Wilayah Lain')->assertDontSee('Petugas Nonaktif');
    $this->from(route('chat.show', $chat))->post(route('admin.penyaluran.store', $lowongan), ['pencari_kerja_id' => $pencari->id])
        ->assertRedirect(route('chat.show', $chat))->assertSessionHasNoErrors()
        ->assertSessionHas('success', fn ($pesan) => str_contains($pesan, 'Petugas Domisili'));
    $proses = Penyaluran::where('pencari_kerja_id', $pencari->id)->firstOrFail();
    expect($proses->disalurkan_oleh)->toBe($adminUtama->id)->and($proses->status->value)->toBe('merekrut');
    $this->get(route('chat.show', $chat))->assertOk()->assertSee('Proses sudah tercatat')
        ->assertDontSee('Rekrut & Teruskan ke Admin Lapangan', false);
    $this->actingAs($user)->getJson(route('chat.messages', $chat))->assertJsonCount(2, 'messages')
        ->assertJsonPath('messages.1.mine', false);
    expect($chat->messages()->where('is_admin', true)->first()->body)->toContain($pencari->kecamatan->nama);

    $this->actingAs($adminWilayah)->get(route('admin.dashboard'))->assertOk()
        ->assertSee('Rekrutmen dari Admin Utama (1)')->assertSee($pencari->nama)->assertSee($lowongan->judul);
    $this->get(route('admin.penyaluran.index', ['dari_admin_utama' => 1]))->assertOk()->assertSee($pencari->nama);
    $this->get(route('chat.show', $chat))->assertForbidden();
    $this->actingAs($adminLain)->get(route('admin.dashboard'))->assertOk()->assertDontSee($pencari->nama);
    $this->get(route('admin.penyaluran.index', ['dari_admin_utama' => 1]))->assertOk()->assertDontSee($pencari->nama);
    $this->put(route('admin.penyaluran.tahap', $proses), ['status' => 'bersertifikat'])->assertForbidden();
    $this->actingAs($adminWilayah)->put(route('admin.penyaluran.tahap', $proses), ['status' => 'bersertifikat'])
        ->assertSessionHasNoErrors();
    $this->get(route('admin.dashboard'))->assertOk()->assertSee('Rekrutmen dari Admin Utama (0)');
});

it('rekrutmen tanpa petugas wilayah tetap tercatat dan tampil saat admin lapangan ditugaskan', function () {
    $user = pencariChat();
    $pencari = $user->pencariKerja;
    $lowongan = JobListing::factory()->aktif()->create();
    $chat = mulaiChat($this, $user, $lowongan);
    $this->actingAs(buatUser(Role::SuperAdmin))->get(route('chat.show', $chat))->assertOk()
        ->assertSee('Belum ada Admin Lapangan aktif');
    $this->post(route('admin.penyaluran.store', $lowongan), ['pencari_kerja_id' => $pencari->id])
        ->assertSessionHasNoErrors()->assertSessionHas('success', fn ($pesan) => str_contains($pesan, 'Belum ada Admin Lapangan aktif'));
    $this->post(route('admin.penyaluran.store', $lowongan), ['pencari_kerja_id' => $pencari->id])
        ->assertSessionHasErrors('pencari_kerja_id');
    expect(Penyaluran::where('pencari_kerja_id', $pencari->id)->count())->toBe(1)
        ->and($chat->messages()->where('is_admin', true)->count())->toBe(1);
    $adminBaru = buatUser(Role::AdminLapangan, [$pencari->kecamatan_id]);
    $this->actingAs($adminBaru)->get(route('admin.penyaluran.index', ['dari_admin_utama' => 1]))
        ->assertOk()->assertSee($pencari->nama);
});

it('filter rekrutmen admin utama membedakan rekrutmen lokal dan mendukung petugas beberapa kecamatan', function () {
    $user = pencariChat();
    $pencari = $user->pencariKerja;
    $wilayahLain = buatKecamatan();
    $admin = buatUser(Role::AdminLapangan, [$pencari->kecamatan_id, $wilayahLain->id]);
    $lowongan = JobListing::factory()->aktif()->create();
    $lokal = PencariKerja::factory()->diKecamatan($wilayahLain)->create(['nama' => 'Rekrutmen Mandiri Lapangan']);
    $this->actingAs(buatUser(Role::SuperAdmin))->post(route('admin.penyaluran.store', $lowongan), ['pencari_kerja_id' => $pencari->id])
        ->assertSessionHasNoErrors();
    $this->actingAs($admin)->post(route('admin.penyaluran.store', $lowongan), ['pencari_kerja_id' => $lokal->id])
        ->assertSessionHasNoErrors();
    $this->get(route('admin.penyaluran.index'))->assertOk()->assertSee($pencari->nama)->assertSee($lokal->nama);
    $this->get(route('admin.penyaluran.index', ['dari_admin_utama' => 1]))->assertOk()->assertSee($pencari->nama)->assertDontSee($lokal->nama);
});
