<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $fillable = [
        'user_id', 'nomor_hp', 'alamat', 'foto', 'tanggal_lahir',
        'poin', 'total_kunjungan', 'total_belanja', 'terakhir_kunjungan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'terakhir_kunjungan' => 'datetime',
        'total_belanja' => 'decimal:2',
        'poin' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
