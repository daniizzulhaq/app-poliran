<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('job_listing_id')->constrained()->restrictOnDelete();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['user_id', 'job_listing_id']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_chat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->restrictOnDelete();
            $table->string('client_id', 36);
            $table->boolean('is_admin');
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['job_chat_id', 'client_id']);
            $table->index(['job_chat_id', 'is_admin', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('job_chats');
    }
};
