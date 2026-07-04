<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruangan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'kapasitas',
        'tarif_per_jam',
        'fasilitas',
        'foto',
        'status',
    ];

    protected $casts = [
        'tarif_per_jam' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'ruangan_id');
    }

    public function bookingsAktif()
    {
        return $this->hasMany(Booking::class, 'ruangan_id')
            ->whereIn('status', ['pending', 'confirmed', 'ongoing']);
    }
}
