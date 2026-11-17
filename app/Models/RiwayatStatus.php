<?php

namespace App\Models;

use App\Enums\StatusPendaftar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatus extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'riwayat_status';

    protected $fillable = ['pencari_kerja_id', 'status_lama', 'status_baru', 'user_id', 'catatan'];

    protected function casts(): array
    {
        return [
            'status_lama' => StatusPendaftar::class,
            'status_baru' => StatusPendaftar::class,
        ];
    }

    public function pencariKerja(): BelongsTo
    {
        return $this->belongsTo(PencariKerja::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
