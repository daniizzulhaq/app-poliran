<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pencari_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('nama', 100);
            $table->char('nik', 16)->unique();
            $table->string('jenis_kelamin', 1);
            $table->date('tanggal_lahir');
            $table->string('pendidikan', 20);
            $table->string('jurusan', 100)->nullable();
            $table->string('no_wa', 20)->nullable();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->restrictOnDelete();
            $table->foreignId('desa_id')->constrained('desa')->restrictOnDelete();
            $table->text('alamat_detail');
            $table->text('kemampuan_lain')->nullable();
            $table->text('pengalaman_kerja')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('status', 20)->default('terdaftar')->index();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('kategori_pencari_kerja', function (Blueprint $table) {
            $table->foreignId('pencari_kerja_id')->constrained('pencari_kerja')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->primary(['pencari_kerja_id', 'kategori_id']);
        });

        Schema::create('pencari_kerja_skill', function (Blueprint $table) {
            $table->foreignId('pencari_kerja_id')->constrained('pencari_kerja')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skill')->cascadeOnDelete();
            $table->primary(['pencari_kerja_id', 'skill_id']);
        });

        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pencari_kerja_id')->constrained('pencari_kerja')->cascadeOnDelete();
            $table->string('status_lama', 20)->nullable();
            $table->string('status_baru', 20);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
        Schema::dropIfExists('pencari_kerja_skill');
        Schema::dropIfExists('kategori_pencari_kerja');
        Schema::dropIfExists('pencari_kerja');
    }
};
