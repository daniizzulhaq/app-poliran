<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = ['sender_id', 'client_id', 'is_admin', 'body', 'read_at'];

    protected function casts(): array
    {
        return ['is_admin' => 'boolean', 'read_at' => 'datetime'];
    }

    public function jobChat(): BelongsTo
    {
        return $this->belongsTo(JobChat::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
