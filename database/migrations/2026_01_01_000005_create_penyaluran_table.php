<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyaluran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_listing_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('pencari_kerja_id')->constrained('pencari_kerja')->cascadeOnDelete();
            $table->string('status', 20)->default('diproses')->index();
            $table->text('catatan')->nullable();
            $table->foreignId('disalurkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hasil_pada')->nullable();
            $table->timestamps();
            $table->unique(['job_listing_id', 'pencari_kerja_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyaluran');
    }
};
