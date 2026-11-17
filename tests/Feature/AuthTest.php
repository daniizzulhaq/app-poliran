<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

it('bisa masuk dan keluar', function () {
    $user = buatUser(Role::SuperAdmin, atribut: ['email' => 'a@a.test']);

    $this->post(route('login'), ['email' => 'a@a.test', 'password' => 'password'])->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);

    $this->post(route('logout'))->assertRedirect(route('home'));
    $this->assertGuest();
});

it('password salah ditolak', function () {
    buatUser(Role::SuperAdmin, atribut: ['email' => 'a@a.test']);
    $this->post(route('login'), ['email' => 'a@a.test', 'password' => 'salah'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('pencari kerja bisa daftar akun dengan persetujuan data', function () {
    $this->post(route('register.pencari-kerja'), [
        'name' => 'Budi', 'email' => 'budi@test.id', 'phone' => '081234567890',
        'password' => 'rahasia123', 'password_confirmation' => 'rahasia123', 'persetujuan' => '1',
    ])->assertRedirect();

    $user = User::where('email', 'budi@test.id')->first();
    expect($user->hasRole('pencari_kerja'))->toBeTrue()
        ->and($user->persetujuan_data_at)->not->toBeNull();
});

it('daftar tanpa persetujuan ditolak', function () {
    $this->post(route('register.pencari-kerja'), [
        'name' => 'Budi', 'email' => 'budi@test.id', 'phone' => '081234567890',
        'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors('persetujuan');
});

it('instansi bisa daftar akun', function () {
    $this->post(route('register.instansi'), [
        'name' => 'Pak Toko', 'email' => 'toko@test.id', 'phone' => '081234567890',
        'password' => 'rahasia123', 'password_confirmation' => 'rahasia123', 'persetujuan' => '1',
    ])->assertRedirect();

    expect(User::where('email', 'toko@test.id')->first()->hasRole('instansi'))->toBeTrue();
});

it('lupa password mengirim tautan reset', function () {
    Notification::fake();
    $user = buatUser(Role::PencariKerja);

    $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('success');
    Notification::assertSentTo($user, ResetPassword::class);
});
