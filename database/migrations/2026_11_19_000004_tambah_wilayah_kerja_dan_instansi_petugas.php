<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - Lowongan: wilayah kerja (dalam TTS / luar TTS / luar negeri) + izin P3MI + tanda legalitas sudah diperiksa.
 * - Instansi boleh tanpa akun bila diinput petugas (jemput bola).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_listings', function (Blueprint $table) {
            $table->string('wilayah_kerja', 20)->default('dalam_tts')->after('lokasi_kerja')->index();
            $table->string('nomor_izin_p3mi', 100)->nullable()->after('wilayah_kerja');
            $table->timestamp('legalitas_diperiksa_pada')->nullable()->after('disurvei_pada');
            $table->foreignId('dibuat_oleh')->nullable()->after('instansi_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('instansi', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('instansi', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('dibuat_oleh')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('instansi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dibuat_oleh');
            $table->dropForeign(['user_id']);
        });
        Schema::table('instansi', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dibuat_oleh');
            $table->dropColumn(['wilayah_kerja', 'nomor_izin_p3mi', 'legalitas_diperiksa_pada']);
        });
    }
};
