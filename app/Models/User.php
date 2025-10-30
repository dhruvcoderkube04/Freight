<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'type',
        'user_type',
        'google_token',
        'facebook_token',
        'provider',
        'provider_id',
        'avatar',
        'auto_approved'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_token',
        'facebook_token'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'auto_approved' => 'boolean',
        ];
    }

    // Relationships
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}