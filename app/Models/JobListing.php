<?php

namespace App\Models;

use App\Enums\JenisKelamin;
use App\Enums\Pendidikan;
use App\Enums\StatusLowongan;
use App\Enums\TipeKerja;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobListing extends Model
{
    use HasFactory;

    protected $table = 'job_listings';

    protected $fillable = [
        'instansi_id', 'kategori_id', 'judul', 'jobdesk', 'pendidikan_min', 'umur_min', 'umur_max', 'jenis_kelamin',
        'pengalaman', 'syarat_lain', 'gaji_min', 'gaji_max', 'gaji_nego', 'fasilitas', 'ketentuan_lain', 'tipe_kerja',
        'lokasi_kerja', 'kuota', 'jumlah_tersalur', 'batas_tanggal', 'status', 'catatan_survei', 'alasan_tolak',
        'disurvei_oleh', 'disurvei_pada', 'ditutup_oleh', 'ditutup_pada',
    ];

    protected function casts(): array
    {
        return [
            'pendidikan_min' => Pendidikan::class,
            'jenis_kelamin' => JenisKelamin::class,
            'tipe_kerja' => TipeKerja::class,
            'status' => StatusLowongan::class,
            'gaji_nego' => 'boolean',
            'umur_min' => 'integer',
            'umur_max' => 'integer',
            'gaji_min' => 'integer',
            'gaji_max' => 'integer',
            'kuota' => 'integer',
            'jumlah_tersalur' => 'integer',
            'batas_tanggal' => 'date',
            'disurvei_pada' => 'datetime',
            'ditutup_pada' => 'datetime',
        ];
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_listing_skill');
    }

    public function penyaluran(): HasMany
    {
        return $this->hasMany(Penyaluran::class);
    }

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disurvei_oleh');
    }

    /** Lowongan aktif: status aktif dan belum lewat batas tanggal (dicek saat query). */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', StatusLowongan::Aktif)->whereDate('batas_tanggal', '>=', today());
    }

    /** Batasi lowongan yang boleh dilihat user. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->can('lihat-lowongan')) {
            return $query;
        }

        if ($user->can('kelola-lowongan-sendiri') && $user->instansi) {
            return $query->where('instansi_id', $user->instansi->id);
        }

        return $query->whereRaw('1 = 0');
    }

    /** Tandai kedaluwarsa semua lowongan aktif yang lewat batas tanggal. */
    public static function tandaiKedaluwarsa(): int
    {
        return static::query()
            ->where('status', StatusLowongan::Aktif)
            ->whereDate('batas_tanggal', '<', today())
            ->update(['status' => StatusLowongan::Kedaluwarsa->value, 'ditutup_pada' => now()]);
    }

    public function isAktif(): bool
    {
        return $this->status === StatusLowongan::Aktif && $this->batas_tanggal->gte(today());
    }

    public function sisaKuota(): int
    {
        return max(0, $this->kuota - $this->jumlah_tersalur);
    }

    public function gajiLabel(): string
    {
        if (! $this->gaji_min && ! $this->gaji_max) {
            return $this->gaji_nego ? 'Nego' : '-';
        }
        $rp = fn (?int $n) => $n ? 'Rp'.number_format($n, 0, ',', '.') : null;
        $teks = collect([$rp($this->gaji_min), $rp($this->gaji_max)])->filter()->implode(' - ');

        return $teks.($this->gaji_nego ? ' (bisa nego)' : '');
    }

    public function umurLabel(): string
    {
        if (! $this->umur_min && ! $this->umur_max) {
            return 'Tanpa batas';
        }

        return ($this->umur_min ?? '...').' - '.($this->umur_max ?? '...').' tahun';
    }
}
