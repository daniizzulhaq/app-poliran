<?php

use App\Enums\Role;

/*
 * Setiap role hanya bisa membuka menu miliknya.
 * Daftar rute => role yang BOLEH membuka.
 */
dataset('menu', function () {
    return [
        'akun admin' => ['admin.users.index', [Role::SuperAdmin]],
        'kecamatan' => ['admin.kecamatan.index', [Role::SuperAdmin]],
        'kategori' => ['admin.kategori.index', [Role::SuperAdmin]],
        'skill' => ['admin.skill.index', [Role::SuperAdmin]],
        'dashboard admin' => ['admin.dashboard', [Role::SuperAdmin, Role::AdminSurvey, Role::AdminLapangan]],
        'dashboard instansi' => ['instansi.dashboard', [Role::Instansi]],
        'dashboard pencari' => ['pencari.dashboard', [Role::PencariKerja]],
    ];
});

it('hanya role yang berhak bisa membuka menu', function (string $route, array $boleh) {
    foreach (Role::cases() as $role) {
        $user = buatUser($role);
        $response = $this->actingAs($user)->get(route($route));

        if (in_array($role, $boleh, true)) {
            $response->assertOk();
        } else {
            $response->assertForbidden();
        }
    }
})->with('menu');

it('tamu diarahkan ke halaman masuk', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->get(route('instansi.dashboard'))->assertRedirect(route('login'));
    $this->get(route('pencari.dashboard'))->assertRedirect(route('login'));
});

it('dashboard mengarahkan sesuai role', function () {
    $this->actingAs(buatUser(Role::AdminSurvey))->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    $this->actingAs(buatUser(Role::Instansi))->get(route('dashboard'))->assertRedirect(route('instansi.dashboard'));
    $this->actingAs(buatUser(Role::PencariKerja))->get(route('dashboard'))->assertRedirect(route('pencari.dashboard'));
});

it('akun nonaktif dikeluarkan', function () {
    $user = buatUser(Role::SuperAdmin, atribut: ['is_active' => false]);
    $this->actingAs($user)->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->assertGuest();
});
