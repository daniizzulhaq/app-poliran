<?php

namespace App\Models;

use App\Enums\StatusTindakLanjut;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjut extends Model
{
    /** Tindak lanjut dilakukan pada bulan ke-1, 3, dan 6 setelah mulai kerja. */
    public const BULAN = [1, 3, 6];

    protected $table = 'tindak_lanjut';

    protected $fillable = ['penyaluran_id', 'bulan_ke', 'status', 'catatan', 'dicatat_oleh'];

    protected function casts(): array
    {
        return [
            'bulan_ke' => 'integer',
            'status' => StatusTindakLanjut::class,
        ];
    }

    public function penyaluran(): BelongsTo
    {
        return $this->belongsTo(Penyaluran::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
