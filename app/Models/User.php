<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // ==========================================
    // KONSTANTA ROLE (Agar tidak salah ketik)
    // ==========================================
    const ROLE_CUSTOMER = 'customer';
    const ROLE_ADMIN = 'admin'; 
    const ROLE_SUPER_ADMIN = 'super_admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'status',
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

    // ==========================================
    // HELPER METHODS UNTUK CEK ROLE & STATUS
    // ==========================================

    public function isAdmin()
    {
        // Admin biasa ATAU Super Admin dianggap sebagai admin
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isSuperAdmin()
    {
        // Khusus hanya untuk Owner
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isCustomer()
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    // ==========================================
    // RELASI (Relationships) ke Tabel Lain
    // ==========================================

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}