<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SplitPayment extends Model
{
    protected $fillable = [
        'transaksi_id', 'order_id', 'metode_bayar', 'jumlah', 'nominal_bayar', 'kembalian',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
