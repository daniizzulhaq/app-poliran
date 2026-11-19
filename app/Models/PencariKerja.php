<?php

namespace App\Models;

use App\Enums\CaraPersetujuan;
use App\Enums\JenisKelamin;
use App\Enums\Pendidikan;
use App\Enums\StatusPendaftar;
use App\Enums\StatusPenyaluran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PencariKerja extends Model
{
    use HasFactory;

    protected $table = 'pencari_kerja';

    protected $fillable = [
        'user_id', 'nama', 'nik', 'jenis_kelamin', 'tanggal_lahir', 'pendidikan', 'jurusan', 'no_wa', 'kecamatan_id',
        'desa_id', 'alamat_detail', 'kemampuan_lain', 'pengalaman_kerja', 'cv_path', 'status', 'dibuat_oleh',
        'persetujuan_cara', 'persetujuan_pada',
    ];

    /** Pendaftar "terdaftar" yang tidak ada perkembangan selama ini dianggap menunggu terlalu lama. */
    public const BATAS_HARI_MENUNGGU = 30;

    protected function casts(): array
    {
        return [
            'jenis_kelamin' => JenisKelamin::class,
            'pendidikan' => Pendidikan::class,
            'status' => StatusPendaftar::class,
            'tanggal_lahir' => 'date',
            'persetujuan_cara' => CaraPersetujuan::class,
            'persetujuan_pada' => 'datetime',
        ];
    }

    /**
     * Masih "terdaftar", tidak ada perubahan status dan tidak sedang dalam penyaluran
     * selama BATAS_HARI_MENUNGGU hari. Supaya warga tidak terlupakan.
     */
    public function scopeMenungguLama(Builder $query): Builder
    {
        $batas = now()->subDays(self::BATAS_HARI_MENUNGGU);

        return $query->where('status', StatusPendaftar::Terdaftar)
            ->where('created_at', '<=', $batas)
            ->whereDoesntHave('riwayatStatus', fn (Builder $q) => $q->where('created_at', '>', $batas))
            ->whereDoesntHave('penyaluran', fn (Builder $q) => $q->whereIn('status', StatusPenyaluran::berjalan()));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function kategori(): BelongsToMany
    {
        return $this->belongsToMany(Kategori::class, 'kategori_pencari_kerja');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'pencari_kerja_skill');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class)->latest('id');
    }

    public function penyaluran(): HasMany
    {
        return $this->hasMany(Penyaluran::class);
    }

    public function pesertaPelatihan(): HasMany
    {
        return $this->hasMany(PesertaPelatihan::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /** Batasi data pendaftar sesuai hak akses user (per kecamatan untuk Admin Lapangan). */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        if ($user->isAdminLapangan()) {
            return $query->whereIn('kecamatan_id', $user->kecamatanIds());
        }

        return $query->where('user_id', $user->id);
    }

    public function nikTersensor(): string
    {
        return self::sensorNik($this->nik);
    }

    /** 5302********0001 */
    public static function sensorNik(?string $nik): string
    {
        if (! $nik || strlen($nik) < 8) {
            return '-';
        }

        return substr($nik, 0, 4).str_repeat('*', strlen($nik) - 8).substr($nik, -4);
    }

    public function umur(): int
    {
        return (int) $this->tanggal_lahir->diffInYears(today());
    }

    public function noWaInternasional(): ?string
    {
        return self::formatWa($this->no_wa);
    }

    public static function formatWa(?string $nomor): ?string
    {
        if (! $nomor) {
            return null;
        }
        $digit = preg_replace('/\D/', '', $nomor);

        return str_starts_with($digit, '0') ? '62'.substr($digit, 1) : $digit;
    }
}
