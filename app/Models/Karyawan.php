<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nama',
        'jabatan',
        'nomor_hp',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
