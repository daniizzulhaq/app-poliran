<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Status penyaluran: diproses/diterima -> tahapan merekrut ... menyalurkan. */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('penyaluran')->where('status', 'diproses')->update(['status' => 'merekrut']);
        DB::table('penyaluran')->where('status', 'diterima')->update(['status' => 'menyalurkan']);

        Schema::table('penyaluran', function (Blueprint $table) {
            $table->string('status', 20)->default('merekrut')->change();
        });
    }

    public function down(): void
    {
        DB::table('penyaluran')->whereIn('status', ['merekrut', 'bersertifikat', 'non_bersertifikat'])->update(['status' => 'diproses']);
        DB::table('penyaluran')->where('status', 'menyalurkan')->update(['status' => 'diterima']);

        Schema::table('penyaluran', function (Blueprint $table) {
            $table->string('status', 20)->default('diproses')->change();
        });
    }
};
