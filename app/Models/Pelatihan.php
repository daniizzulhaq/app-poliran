<?php

namespace App\Models;

use App\Enums\StatusPelatihan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Pelatihan extends Model
{
    use HasFactory;

    protected $table = 'pelatihan';

    protected $fillable = [
        'kategori_id', 'kecamatan_id', 'nama', 'slug', 'deskripsi', 'penyelenggara', 'mitra', 'tanggal_mulai',
        'tanggal_selesai', 'jam_mulai', 'jam_selesai', 'alamat', 'kuota', 'syarat', 'batas_pendaftaran',
        'bersertifikat', 'poster', 'status', 'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPelatihan::class,
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'batas_pendaftaran' => 'date',
            'bersertifikat' => 'boolean',
            'kuota' => 'integer',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(PesertaPelatihan::class);
    }

    public function scopeDibuka(Builder $query): Builder
    {
        return $query->where('status', StatusPelatihan::Dibuka);
    }

    /** Bisa didaftar: status dibuka dan belum lewat batas pendaftaran. */
    public function bisaDidaftar(): bool
    {
        return $this->status === StatusPelatihan::Dibuka && $this->batas_pendaftaran->gte(today());
    }

    public function penyelenggaraLabel(): string
    {
        return $this->penyelenggara.($this->mitra ? ' bersama '.$this->mitra : '');
    }

    public function posterUrl(): ?string
    {
        return $this->poster ? Storage::disk('public')->url($this->poster) : null;
    }

    public function jamLabel(): string
    {
        return substr((string) $this->jam_mulai, 0, 5).($this->jam_selesai ? ' - '.substr((string) $this->jam_selesai, 0, 5) : '').' WITA';
    }
}
