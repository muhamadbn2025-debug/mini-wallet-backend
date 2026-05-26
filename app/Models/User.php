<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- tambah ini

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <-- tambah HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'phone',     // <-- tambah phone
        'password',
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

    // Relasi ke Wallet
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}