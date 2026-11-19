<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Data kerja setelah disalurkan + tindak lanjut bulan ke-1, 3, 6 (masih bekerja atau tidak). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyaluran', function (Blueprint $table) {
            $table->date('tanggal_mulai_kerja')->nullable()->after('hasil_pada');
            $table->unsignedBigInteger('gaji')->nullable()->after('tanggal_mulai_kerja');
        });

        DB::table('penyaluran')
            ->where('status', 'menyalurkan')
            ->whereNull('tanggal_mulai_kerja')
            ->update(['tanggal_mulai_kerja' => DB::raw('DATE(COALESCE(hasil_pada, updated_at))')]);

        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyaluran_id')->constrained('penyaluran')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan_ke');
            $table->string('status', 20)->index();
            $table->text('catatan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['penyaluran_id', 'bulan_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
        Schema::table('penyaluran', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai_kerja', 'gaji']);
        });
    }
};
