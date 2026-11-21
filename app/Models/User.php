<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'address_detail',
        'is_guest',
        'tier',
        'internal_notes',
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
        /**
     * URL avatar yang aman: null bila file tidak ada, agar UI jatuh ke inisial.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function (?string $value) {
            if (!$value) return null;
            if (str_starts_with($value, 'http')) return $value;

            return Storage::disk('public')->exists($value) ? Storage::url($value) : null;
        });
    }

    // ==========================================
    // HELPER METHODS UNTUK CEK ROLE & STATUS
    // ==========================================

    public function isAdmin()
    {
        // Admin biasa ATAU Super Admin dianggap sebagai admin
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_SUPER_ADMIN;
    }

   public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'owner']);
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