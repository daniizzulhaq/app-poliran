<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelatihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->restrictOnDelete();
            $table->string('nama', 150);
            $table->string('slug', 180)->unique();
            $table->text('deskripsi');
            $table->string('penyelenggara', 150)->default('Polres Timor Tengah Selatan');
            $table->string('mitra', 150)->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();
            $table->text('alamat');
            $table->unsignedInteger('kuota');
            $table->text('syarat')->nullable();
            $table->date('batas_pendaftaran');
            $table->boolean('bersertifikat')->default(true);
            $table->string('poster')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('peserta_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_id')->constrained('pelatihan')->cascadeOnDelete();
            $table->foreignId('pencari_kerja_id')->constrained('pencari_kerja')->cascadeOnDelete();
            $table->text('alasan');
            $table->text('pengalaman_bidang')->nullable();
            $table->string('status', 20)->default('mendaftar')->index();
            $table->text('catatan')->nullable();
            $table->string('nomor_sertifikat', 60)->nullable()->unique();
            $table->string('sertifikat_path')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['pelatihan_id', 'pencari_kerja_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_pelatihan');
        Schema::dropIfExists('pelatihan');
    }
};
