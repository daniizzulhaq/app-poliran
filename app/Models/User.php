<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'tanggal_lahir', 'password', 'is_active', 'persetujuan_data_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    /** @var array<int, int>|null */
    private ?array $kecamatanIdsCache = null;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'persetujuan_data_at' => 'datetime',
            'tanggal_lahir' => 'date',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function instansi(): HasOne
    {
        return $this->hasOne(Instansi::class);
    }

    public function pencariKerja(): HasOne
    {
        return $this->hasOne(PencariKerja::class);
    }

    /** Kecamatan yang ditugaskan (khusus Admin Lapangan). */
    public function kecamatan(): BelongsToMany
    {
        return $this->belongsToMany(Kecamatan::class, 'admin_kecamatan');
    }

    public function isRole(Role $role): bool
    {
        return $this->hasRole($role->value);
    }

    public function isSuperAdmin(): bool
    {
        return $this->isRole(Role::SuperAdmin);
    }

    public function isAdminLapangan(): bool
    {
        return $this->isRole(Role::AdminLapangan);
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(array_map(fn (Role $r) => $r->value, Role::adminRoles()));
    }

    public function roleEnum(): ?Role
    {
        $name = $this->getRoleNames()->first();

        return $name ? Role::tryFrom($name) : null;
    }

    /** @return array<int, int> */
    public function kecamatanIds(): array
    {
        return $this->kecamatanIdsCache ??= $this->kecamatan()->pluck('kecamatan.id')->map(fn ($id) => (int) $id)->all();
    }

    /** Apakah admin boleh mengelola data di kecamatan ini. */
    public function bolehKelolaKecamatan(int $kecamatanId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->isAdminLapangan() && in_array($kecamatanId, $this->kecamatanIds(), true);
    }
}
