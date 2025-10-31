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

    // Check if user is admin
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    // Check if user is manager
    public function isManager()
    {
        return $this->user_type === 'manager';
    }

    // Check if user is regular user
    public function isUser()
    {
        return $this->user_type === 'user';
    }

    // Check if user registered via socialite
    public function isSocialUser()
    {
        return in_array($this->type, ['google', 'facebook']);
    }

    // Check if user registered via email
    public function isEmailUser()
    {
        return $this->type === 'email';
    }

    // Get social token based on provider type
    public function getSocialToken()
    {
        if ($this->type === 'google') {
            return $this->google_token;
        } elseif ($this->type === 'facebook') {
            return $this->facebook_token;
        }
        return null;
    }

    // Set social token based on provider type
    public function setSocialToken($token)
    {
        if ($this->type === 'google') {
            $this->google_token = $token;
        } elseif ($this->type === 'facebook') {
            $this->facebook_token = $token;
        }
    }
}