<?php

namespace App\Models;

use App\Enums\JenisDokumen;
use App\Enums\JenisInstansi;
use App\Enums\StatusInstansi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instansi extends Model
{
    use HasFactory;

    protected $table = 'instansi';

    protected $fillable = [
        'user_id', 'dibuat_oleh', 'nama', 'jenis', 'penanggung_jawab', 'alamat', 'no_wa', 'jenis_dokumen', 'nomor_dokumen',
        'file_dokumen', 'file_selfie', 'latitude', 'longitude', 'status', 'catatan_survei', 'alasan_tolak',
        'disurvei_oleh', 'disurvei_pada', 'diajukan_pada',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => JenisInstansi::class,
            'jenis_dokumen' => JenisDokumen::class,
            'status' => StatusInstansi::class,
            'latitude' => 'float',
            'longitude' => 'float',
            'disurvei_pada' => 'datetime',
            'diajukan_pada' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Petugas yang menginput instansi (jemput bola); null bila instansi mendaftar sendiri. */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disurvei_oleh');
    }

    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }

    public function isTerverifikasi(): bool
    {
        return $this->status === StatusInstansi::Terverifikasi;
    }

    public function googleMapsUrl(): string
    {
        return 'https://www.google.com/maps?q='.$this->latitude.','.$this->longitude;
    }
}
