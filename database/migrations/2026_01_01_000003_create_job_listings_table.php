<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instansi_id')->constrained('instansi')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->string('judul', 150);
            $table->text('jobdesk');
            $table->string('pendidikan_min', 20)->nullable();
            $table->unsignedTinyInteger('umur_min')->nullable();
            $table->unsignedTinyInteger('umur_max')->nullable();
            $table->string('jenis_kelamin', 1)->nullable();
            $table->text('pengalaman')->nullable();
            $table->text('syarat_lain')->nullable();
            $table->unsignedBigInteger('gaji_min')->nullable();
            $table->unsignedBigInteger('gaji_max')->nullable();
            $table->boolean('gaji_nego')->default(false);
            $table->text('fasilitas')->nullable();
            $table->text('ketentuan_lain')->nullable();
            $table->string('tipe_kerja', 20);
            $table->string('lokasi_kerja', 200);
            $table->unsignedInteger('kuota');
            $table->unsignedInteger('jumlah_tersalur')->default(0);
            $table->date('batas_tanggal');
            $table->string('status', 20)->default('menunggu_survey')->index();
            $table->text('catatan_survei')->nullable();
            $table->text('alasan_tolak')->nullable();
            $table->foreignId('disurvei_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disurvei_pada')->nullable();
            $table->foreignId('ditutup_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ditutup_pada')->nullable();
            $table->timestamps();
        });

        Schema::create('job_listing_skill', function (Blueprint $table) {
            $table->foreignId('job_listing_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skill')->cascadeOnDelete();
            $table->primary(['job_listing_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listing_skill');
        Schema::dropIfExists('job_listings');
    }
};
