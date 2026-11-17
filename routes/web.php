<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Instansi;
use App\Http\Controllers\PencariKerja;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::view('/', 'public.home')->name('home');
Route::view('/pelatihan', 'public.home')->name('public.pelatihan.index');
Route::view('/tentang', 'public.home')->name('public.tentang');
Route::view('/kontak', 'public.home')->name('public.kontak');

Route::get('/wilayah/kecamatan/{kecamatan}/desa', [WilayahController::class, 'desa'])->name('wilayah.desa');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'create'])->name('login');
    Route::post('/masuk', [LoginController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/daftar', [RegisterController::class, 'pilih'])->name('register');
    Route::get('/daftar/pencari-kerja', [RegisterController::class, 'createPencariKerja'])->name('register.pencari-kerja');
    Route::post('/daftar/pencari-kerja', [RegisterController::class, 'storePencariKerja'])->middleware('throttle:10,1');
    Route::get('/daftar/instansi', [RegisterController::class, 'createInstansi'])->name('register.instansi');
    Route::post('/daftar/instansi', [RegisterController::class, 'storeInstansi'])->middleware('throttle:10,1');

    Route::get('/lupa-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'email'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware(['auth', 'aktif'])->group(function () {
    Route::post('/keluar', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [AkunController::class, 'dashboard'])->name('dashboard');
    Route::get('/akun/password', [AkunController::class, 'editPassword'])->name('akun.password.edit');
    Route::put('/akun/password', [AkunController::class, 'updatePassword'])->name('akun.password.update');

    /*
    |--------------------------------------------------------------------------
    | Admin (Super Admin, Admin Survei, Admin Lapangan)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:super_admin|admin_survey|admin_lapangan')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::middleware('permission:kelola-admin')->group(function () {
            Route::resource('users', Admin\AdminUserController::class)->except(['show', 'destroy']);
        });

        Route::middleware('permission:kelola-master-data')->group(function () {
            Route::resource('kecamatan', Admin\KecamatanController::class)->except(['create', 'edit']);
            Route::post('kecamatan/{kecamatan}/desa', [Admin\KecamatanController::class, 'storeDesa'])->name('desa.store');
            Route::put('kecamatan/{kecamatan}/desa/{desa}', [Admin\KecamatanController::class, 'updateDesa'])->name('desa.update');
            Route::delete('kecamatan/{kecamatan}/desa/{desa}', [Admin\KecamatanController::class, 'destroyDesa'])->name('desa.destroy');
            Route::resource('kategori', Admin\KategoriController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('skill', Admin\SkillController::class)->only(['index', 'store', 'update', 'destroy']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Instansi (penyedia kerja)
    |--------------------------------------------------------------------------
    */
    Route::prefix('instansi')->name('instansi.')->middleware('role:instansi')->group(function () {
        Route::get('/', Instansi\DashboardController::class)->name('dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | Pencari kerja
    |--------------------------------------------------------------------------
    */
    Route::prefix('saya')->name('pencari.')->middleware('role:pencari_kerja')->group(function () {
        Route::get('/', PencariKerja\DashboardController::class)->name('dashboard');
    });
});
