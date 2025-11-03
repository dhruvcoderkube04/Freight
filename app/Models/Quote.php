<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   Quote extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'user_id',
        'pickup_location',
        'drop_location',
        'shipment_date',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pickupDetail()
    {
        return $this->hasOne(PickupDetail::class);
    }

    public function deliveryDetail()
    {
        return $this->hasOne(DeliveryDetail::class);
    }

    public function commodities()
    {
        return $this->hasMany(Commodity::class);
    }

    public function tqlResponses()
    {
        return $this->hasMany(TQLResponse::class);
    }

    public function latestTqlResponse()
    {
        return $this->hasOne(TQLResponse::class)->latest();
    }
}