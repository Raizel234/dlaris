<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'user_id', 'tanggal', 'jam_masuk', 'jam_pulang',
        'status', 'keterangan', 'verified_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
