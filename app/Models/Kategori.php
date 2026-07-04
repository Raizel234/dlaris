<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'ikon',
        'gambar',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($kategori) {
            $kategori->slug = str()->slug($kategori->nama);
        });
        static::updating(function ($kategori) {
            if ($kategori->isDirty('nama')) {
                $kategori->slug = str()->slug($kategori->nama);
            }
        });
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'kategori_id');
    }

    public function menusAktif()
    {
        return $this->hasMany(Menu::class, 'kategori_id')->where('is_tersedia', true);
    }
}
