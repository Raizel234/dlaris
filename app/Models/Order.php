<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'meja_id',
        'promo_id',
        'nomor_order',
        'status',
        'catatan',
        'tipe_pesanan',
        'nama_pelanggan',
        'no_hp',
        'total',
        'diskon',
        'total_setelah_diskon',
        'pajak',
        'service_charge',
        'grand_total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_setelah_diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meja()
    {
        return $this->belongsTo(Meja::class, 'meja_id');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'order_id');
    }

    public function splitPayments()
    {
        return $this->hasMany(SplitPayment::class);
    }

    public function hitungGrandTotal($serviceChargePersen = 0, $pajakPersen = 0)
    {
        $this->total_setelah_diskon = $this->total - $this->diskon;
        $this->service_charge = $this->total_setelah_diskon * $serviceChargePersen / 100;
        $this->pajak = ($this->total_setelah_diskon + $this->service_charge) * $pajakPersen / 100;
        $this->grand_total = $this->total_setelah_diskon + $this->service_charge + $this->pajak;
        return $this;
    }
}
