<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = ['nama', 'slug'];

    public function pelatihan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pelatihan::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Kategori $kategori) {
            $kategori->slug = Str::slug($kategori->nama);
        });
    }
}
