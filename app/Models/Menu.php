<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'menus';

    protected $fillable = [
        'kategori_id',
        'nama',
        'slug',
        'deskripsi',
        'harga',
        'stok',
        'foto',
        'is_tersedia',
        'is_best_seller',
        'is_new',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_tersedia' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_new' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($menu) {
            $menu->slug = str()->slug($menu->nama);
        });
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function bahans()
    {
        return $this->belongsToMany(Bahan::class, 'bahan_menu')
            ->withPivot('jumlah_per_porsi')
            ->withTimestamps();
    }
}
