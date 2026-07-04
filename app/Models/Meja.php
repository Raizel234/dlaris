<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    protected $fillable = [
        'nomor_meja',
        'kapasitas',
        'area',
        'status',
        'qr_code',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'meja_id');
    }
}
