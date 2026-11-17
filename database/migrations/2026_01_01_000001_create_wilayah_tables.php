<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->nullable()->unique();
            $table->string('nama', 100)->unique();
            $table->timestamps();
        });

        Schema::create('desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->string('kode', 20)->nullable();
            $table->string('nama', 100);
            $table->timestamps();
            $table->unique(['kecamatan_id', 'nama']);
        });

        Schema::create('admin_kecamatan', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->primary(['user_id', 'kecamatan_id']);
        });

        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->timestamps();
        });

        Schema::create('skill', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill');
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('admin_kecamatan');
        Schema::dropIfExists('desa');
        Schema::dropIfExists('kecamatan');
    }
};
