<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'ruangan_id',
        'nama_pemesan',
        'nomor_hp',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'durasi',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'total_harga' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'booking_id');
    }
}
