<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\StatusPenyaluran;
use App\Enums\StatusTindakLanjut;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Penyaluran extends Model
{
    protected $table = 'penyaluran';

    protected $fillable = [
        'job_listing_id', 'pencari_kerja_id', 'status', 'catatan', 'disalurkan_oleh', 'diproses_oleh', 'hasil_pada',
        'tanggal_mulai_kerja', 'gaji',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusPenyaluran::class,
            'hasil_pada' => 'datetime',
            'tanggal_mulai_kerja' => 'date',
            'gaji' => 'integer',
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

    public function scopeDariAdminUtama(Builder $query): Builder
    {
        return $query->whereHas('penyalur', fn (Builder $q) => $q->role(Role::SuperAdmin->value));
    }

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class)->orderBy('bulan_ke');
    }

    /** Pekerja tersalur yang punya tindak lanjut jatuh tempo (bulan ke-n sudah lewat dan belum dicatat). */
    public function scopeTindakLanjutJatuhTempo(Builder $query): Builder
    {
        return $query->where('status', StatusPenyaluran::Menyalurkan)
            ->whereDoesntHave('tindakLanjut', fn (Builder $q) => $q->where('status', StatusTindakLanjut::Berhenti))
            ->where(function (Builder $q) {
                foreach (TindakLanjut::BULAN as $bulan) {
                    $q->orWhere(fn (Builder $w) => $w
                        ->whereDate('tanggal_mulai_kerja', '<=', today()->subMonthsNoOverflow($bulan))
                        ->whereDoesntHave('tindakLanjut', fn (Builder $t) => $t->where('bulan_ke', $bulan)));
                }
            });
    }

    /** Bulan tindak lanjut berikutnya (null bila selesai / sudah berhenti / belum tersalur). Butuh relasi tindakLanjut. */
    public function bulanTindakLanjutBerikutnya(): ?int
    {
        if ($this->status !== StatusPenyaluran::Menyalurkan || ! $this->tanggal_mulai_kerja) {
            return null;
        }
        if ($this->tindakLanjut->contains('status', StatusTindakLanjut::Berhenti)) {
            return null;
        }

        $sudah = $this->tindakLanjut->pluck('bulan_ke')->all();

        return collect(TindakLanjut::BULAN)->first(fn (int $b) => ! in_array($b, $sudah, true));
    }

    public function jatuhTempo(int $bulan): Carbon
    {
        return $this->tanggal_mulai_kerja->copy()->addMonthsNoOverflow($bulan);
    }
}
