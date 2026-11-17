<?php

namespace App\Models;

use App\Enums\StatusPenyaluran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penyaluran extends Model
{
    protected $table = 'penyaluran';

    protected $fillable = [
        'job_listing_id', 'pencari_kerja_id', 'status', 'catatan', 'disalurkan_oleh', 'diproses_oleh', 'hasil_pada',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPenyaluran::class,
            'hasil_pada' => 'datetime',
        ];
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }

    public function pencariKerja(): BelongsTo
    {
        return $this->belongsTo(PencariKerja::class);
    }

    public function penyalur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disalurkan_oleh');
    }
}
