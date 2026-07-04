<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $fillable = [
        'kategori', 'judul', 'deskripsi', 'jumlah', 'tanggal', 'bukti', 'created_by',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
