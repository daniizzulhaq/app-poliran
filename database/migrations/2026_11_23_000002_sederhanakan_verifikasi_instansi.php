<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instansi', function (Blueprint $table) {
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_dokumen', 10)->nullable()->change();
            $table->string('nomor_dokumen', 50)->nullable()->change();
            $table->string('file_dokumen')->nullable()->change();
            $table->string('file_selfie')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Tetap izinkan dokumen kosong agar data tanpa unggahan tidak perlu diubah saat rollback.
        Schema::table('instansi', function (Blueprint $table) {
            $table->dropColumn('tanggal_lahir');
        });
    }
};
