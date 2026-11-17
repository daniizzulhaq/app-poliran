<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';

    protected $fillable = ['kode', 'nama'];

    public function desa(): HasMany
    {
        return $this->hasMany(Desa::class)->orderBy('nama');
    }

    public function admin(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admin_kecamatan');
    }

    public function pencariKerja(): HasMany
    {
        return $this->hasMany(PencariKerja::class);
    }
}
