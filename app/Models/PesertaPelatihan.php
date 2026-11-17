<?php

namespace App\Models;

use App\Enums\StatusPeserta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaPelatihan extends Model
{
    protected $table = 'peserta_pelatihan';

    protected $fillable = [
        'pelatihan_id', 'pencari_kerja_id', 'alasan', 'pengalaman_bidang', 'status', 'catatan', 'nomor_sertifikat',
        'sertifikat_path', 'diproses_oleh',
    ];

    protected function casts(): array
    {
        return ['status' => StatusPeserta::class];
    }

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function pencariKerja(): BelongsTo
    {
        return $this->belongsTo(PencariKerja::class);
    }
}
