<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Instansi;
use App\Http\Controllers\PencariKerja;
use App\Http\Controllers\Publik;
use App\Http\Controllers\WilayahController;
use App\Http\Middleware\EnsureProfilLengkapUntukChat;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik (hanya lowongan aktif dan masih tersedia)
|--------------------------------------------------------------------------
*/
Route::get('/', Publik\HomeController::class)->name('home');
Route::get('/lowongan', [Publik\LowonganController::class, 'index'])->name('public.lowongan.index');
Route::get('/lowongan/{lowongan}', [Publik\LowonganController::class, 'show'])->name('public.lowongan.show');
Route::get('/pelatihan', [Publik\PelatihanController::class, 'index'])->name('public.pelatihan.index');
Route::get('/pelatihan/{pelatihan:slug}', [Publik\PelatihanController::class, 'show'])->name('public.pelatihan.show');
Route::get('/tentang', [Publik\HalamanController::class, 'tentang'])->name('public.tentang');
Route::get('/kontak', [Publik\HalamanController::class, 'kontak'])->name('public.kontak');

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
    Route::middleware(['role:super_admin|pencari_kerja', EnsureProfilLengkapUntukChat::class])->prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/belum-dibaca', [ChatController::class, 'unread'])->name('unread');
        Route::get('/minat/{lowongan}', [ChatController::class, 'interest'])->middleware('role:pencari_kerja')->name('interest');
        Route::post('/lowongan/{lowongan}', [ChatController::class, 'start'])->middleware(['role:pencari_kerja', 'throttle:20,1'])->name('start');
        Route::get('/{chat}', [ChatController::class, 'show'])->name('show');
        Route::get('/{chat}/pesan', [ChatController::class, 'messages'])->name('messages');
        Route::post('/{chat}/pesan', [ChatController::class, 'send'])->middleware('throttle:30,1')->name('send');
    });
    Route::get('/akun/password', [AkunController::class, 'editPassword'])->name('akun.password.edit');
    Route::put('/akun/password', [AkunController::class, 'updatePassword'])->name('akun.password.update');

    // Berkas private — setiap akses dicek Policy
    Route::get('/berkas/instansi/{instansi}/{jenis}', [BerkasController::class, 'instansi'])->name('berkas.instansi');
    Route::get('/berkas/pendaftar/{pencariKerja}/cv', [BerkasController::class, 'cv'])->name('berkas.cv');
    Route::get('/berkas/sertifikat/{peserta}', [BerkasController::class, 'sertifikat'])->name('berkas.sertifikat');

    /*
    |--------------------------------------------------------------------------
    | Admin (Super Admin, Admin Survei, Admin Lapangan)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:super_admin|admin_survey|admin_lapangan')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        // Survei instansi
        Route::middleware('permission:survei-instansi')->prefix('survei-instansi')->name('survei-instansi.')->group(function () {
            Route::get('/', [Admin\SurveiInstansiController::class, 'index'])->name('index');
            Route::get('/tambah', [Admin\SurveiInstansiController::class, 'create'])->name('create');
            Route::post('/', [Admin\SurveiInstansiController::class, 'store'])->name('store');
            Route::get('/{instansi}', [Admin\SurveiInstansiController::class, 'show'])->name('show');
            Route::post('/{instansi}/putusan', [Admin\SurveiInstansiController::class, 'putusan'])->name('putusan');
        });

        // Survei lowongan
        Route::middleware('permission:survei-lowongan')->prefix('survei-lowongan')->name('survei-lowongan.')->group(function () {
            Route::get('/', [Admin\SurveiLowonganController::class, 'index'])->name('index');
            Route::post('/{lowongan}/putusan', [Admin\SurveiLowonganController::class, 'putusan'])->name('putusan');
        });

        // Lowongan (internal, hanya admin) + tab pendaftar cocok
        Route::middleware('permission:lihat-lowongan')->prefix('lowongan')->name('lowongan.')->group(function () {
            Route::get('/', [Admin\LowonganController::class, 'index'])->name('index');
            Route::middleware('permission:survei-lowongan')->group(function () {
                Route::get('/tambah', [Admin\LowonganController::class, 'create'])->name('create');
                Route::post('/', [Admin\LowonganController::class, 'store'])->name('store');
            });
            Route::get('/{lowongan}', [Admin\LowonganController::class, 'show'])->name('show');
            Route::post('/{lowongan}/tutup', [Admin\LowonganController::class, 'tutup'])->name('tutup');
        });

        // Pendaftar (dibatasi per kecamatan)
        Route::middleware('permission:kelola-pendaftar')->group(function () {
            Route::resource('pendaftar', Admin\PendaftarController::class)
                ->parameters(['pendaftar' => 'pencariKerja'])->except(['destroy']);
            Route::post('pendaftar/{pencariKerja}/status', [Admin\PendaftarController::class, 'ubahStatus'])->name('pendaftar.status');
        });

        // Penyaluran
        Route::middleware('permission:kelola-penyaluran')->prefix('penyaluran')->name('penyaluran.')->group(function () {
            Route::get('/', [Admin\PenyaluranController::class, 'index'])->name('index');
            Route::post('/lowongan/{lowongan}', [Admin\PenyaluranController::class, 'store'])->name('store');
            Route::put('/{penyaluran}/tahap', [Admin\PenyaluranController::class, 'tahap'])->name('tahap');
            Route::get('/tindak-lanjut', [Admin\TindakLanjutController::class, 'index'])->name('tindak-lanjut');
            Route::post('/{penyaluran}/tindak-lanjut', [Admin\TindakLanjutController::class, 'store'])->name('tindak-lanjut.store');
        });

        // Pelatihan
        Route::middleware('permission:kelola-pelatihan')->group(function () {
            Route::resource('pelatihan', Admin\PelatihanController::class)->except(['destroy']);
            Route::post('pelatihan/{pelatihan}/status', [Admin\PelatihanController::class, 'ubahStatus'])->name('pelatihan.status');
        });

        // Peserta pelatihan (Admin Lapangan hanya kecamatannya)
        Route::middleware('permission:kelola-peserta-pelatihan')->prefix('peserta')->name('peserta.')->group(function () {
            Route::get('/', [Admin\PesertaPelatihanController::class, 'index'])->name('index');
            Route::get('/pelatihan/{pelatihan}', [Admin\PesertaPelatihanController::class, 'pelatihan'])->name('pelatihan');
            Route::put('/{peserta}/seleksi', [Admin\PesertaPelatihanController::class, 'seleksi'])->name('seleksi');
            Route::put('/{peserta}/hasil', [Admin\PesertaPelatihanController::class, 'hasil'])->name('hasil');
        });

        // Konten halaman awal
        Route::middleware('permission:kelola-konten')->group(function () {
            Route::get('konten', [Admin\KontenController::class, 'edit'])->name('konten.edit');
            Route::put('konten', [Admin\KontenController::class, 'update'])->name('konten.update');
            Route::resource('testimoni', Admin\TestimoniController::class)->except(['show']);
        });

        // Laporan & export
        Route::middleware('permission:lihat-laporan')->prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [Admin\LaporanController::class, 'index'])->name('index');
            Route::get('/excel', [Admin\LaporanController::class, 'excel'])->name('excel');
            Route::get('/pdf', [Admin\LaporanController::class, 'pdf'])->name('pdf');
        });

        // Akun admin & master data
        Route::middleware('permission:kelola-admin')->group(function () {
            Route::resource('users', Admin\AdminUserController::class)->except(['show', 'destroy']);
        });

        Route::middleware('permission:kelola-master-data')->group(function () {
            Route::post('kecamatan/sinkronkan', [Admin\KecamatanController::class, 'sinkronkan'])->name('kecamatan.sinkronkan');
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
        Route::get('/verifikasi', [Instansi\VerifikasiController::class, 'edit'])->name('verifikasi.edit');
        Route::put('/verifikasi', [Instansi\VerifikasiController::class, 'update'])->name('verifikasi.update');
        Route::resource('lowongan', Instansi\LowonganController::class)->except(['destroy']);
        Route::post('lowongan/{lowongan}/tutup', [Instansi\LowonganController::class, 'tutup'])->name('lowongan.tutup');
    });

    /*
    |--------------------------------------------------------------------------
    | Pencari kerja
    |--------------------------------------------------------------------------
    */
    Route::prefix('saya')->name('pencari.')->middleware('role:pencari_kerja')->group(function () {
        Route::get('/', PencariKerja\DashboardController::class)->name('dashboard');
        Route::get('/profil', [PencariKerja\ProfilController::class, 'edit'])->name('profil.edit');
        Route::put('/profil', [PencariKerja\ProfilController::class, 'update'])->name('profil.update');
        Route::get('/pelatihan', [PencariKerja\PelatihanController::class, 'index'])->name('pelatihan.index');
    });

    // Form pendaftaran pelatihan (wajib login pencari kerja)
    Route::middleware('role:pencari_kerja')->group(function () {
        Route::get('/pelatihan/{pelatihan:slug}/daftar', [PencariKerja\PelatihanController::class, 'create'])->name('pencari.pelatihan.daftar');
        Route::post('/pelatihan/{pelatihan:slug}/daftar', [PencariKerja\PelatihanController::class, 'store'])->name('pencari.pelatihan.store');
    });
});
