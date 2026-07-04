<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nomor_hp',
        'foto',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    public function isPelanggan(): bool
    {
        return $this->role === 'pelanggan';
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class);
    }

    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function logActivities()
    {
        return $this->hasMany(LogActivity::class);
    }

    public function tambahPoin($poin)
    {
        $this->increment('poin', $poin);
        $this->updateMemberTier();
    }

    public function kurangiPoin($poin)
    {
        $this->decrement('poin', max(0, $poin));
    }

    public function updateMemberTier()
    {
        $tier = 'regular';
        if ($this->poin >= 1000) $tier = 'platinum';
        elseif ($this->poin >= 500) $tier = 'gold';
        elseif ($this->poin >= 100) $tier = 'silver';
        if ($this->member_tier !== $tier) {
            $this->update(['member_tier' => $tier]);
        }
    }
}
