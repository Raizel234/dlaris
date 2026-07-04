<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'order_id',
        'booking_id',
        'user_id',
        'kode_transaksi',
        'metode_bayar',
        'total',
        'nominal_bayar',
        'kembalian',
        'status',
        'alasan_void',
        'is_split',
        'tipe_bayar',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'is_split' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function splitPayments()
    {
        return $this->hasMany(SplitPayment::class);
    }
}
