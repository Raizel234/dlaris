<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'kode', 'nama', 'deskripsi', 'tipe', 'nilai', 'min_belanja', 'maks_diskon',
        'kuota', 'terpakai', 'berlaku_mulai', 'berlaku_sampai',
        'metode_bayar', 'hari', 'is_active',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'min_belanja' => 'decimal:2',
        'maks_diskon' => 'decimal:2',
        'berlaku_mulai' => 'datetime',
        'berlaku_sampai' => 'datetime',
        'metode_bayar' => 'array',
        'hari' => 'array',
        'is_active' => 'boolean',
    ];

    public function isValid($total = 0, $metodeBayar = null)
    {
        if (!$this->is_active) return false;
        if ($this->berlaku_mulai && now()->lt($this->berlaku_mulai)) return false;
        if ($this->berlaku_sampai && now()->gt($this->berlaku_sampai)) return false;
        if ($this->kuota && $this->terpakai >= $this->kuota) return false;
        if ($this->min_belanja > 0 && $total < $this->min_belanja) return false;
        if ($this->hari && !in_array(now()->dayOfWeek, $this->hari)) return false;
        if ($this->metode_bayar && $metodeBayar && !in_array($metodeBayar, $this->metode_bayar)) return false;
        return true;
    }

    public function hitungDiskon($total)
    {
        if ($this->tipe === 'persen') {
            $diskon = $total * $this->nilai / 100;
            if ($this->maks_diskon) {
                $diskon = min($diskon, $this->maks_diskon);
            }
            return $diskon;
        }
        if ($this->tipe === 'nominal') {
            return min($this->nilai, $total);
        }
        return 0;
    }
}
