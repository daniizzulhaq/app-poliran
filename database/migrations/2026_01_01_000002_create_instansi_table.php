<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instansi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nama', 150);
            $table->string('jenis', 20);
            $table->string('penanggung_jawab', 100);
            $table->text('alamat');
            $table->string('no_wa', 20);
            $table->string('jenis_dokumen', 10);
            $table->string('nomor_dokumen', 50);
            $table->string('file_dokumen');
            $table->string('file_selfie');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('status', 20)->default('menunggu_survey')->index();
            $table->text('catatan_survei')->nullable();
            $table->text('alasan_tolak')->nullable();
            $table->foreignId('disurvei_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disurvei_pada')->nullable();
            $table->timestamp('diajukan_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instansi');
    }
};
