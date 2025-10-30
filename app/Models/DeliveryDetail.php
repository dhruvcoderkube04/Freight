<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'city',
        'state',
        'postal_code',
        'country',
        'address_1',
        'address_2',
        'contact_number'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}