<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Catatan persetujuan warga atas penyimpanan & penggunaan datanya (UU PDP). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pencari_kerja', function (Blueprint $table) {
            $table->string('persetujuan_cara', 20)->nullable()->after('cv_path');
            $table->timestamp('persetujuan_pada')->nullable()->after('persetujuan_cara');
        });

        // Pendaftar yang mendaftar sendiri sudah mencentang persetujuan saat membuat akun.
        DB::table('pencari_kerja')
            ->whereNotNull('user_id')
            ->update(['persetujuan_cara' => 'daring', 'persetujuan_pada' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('pencari_kerja', function (Blueprint $table) {
            $table->dropColumn(['persetujuan_cara', 'persetujuan_pada']);
        });
    }
};
