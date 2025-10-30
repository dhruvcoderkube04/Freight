<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commodity extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'quantity',
        'unit_type',
        'freight_class_code',
        'weight',
        'length',
        'width',
        'height',
        'additional_services'
    ];

    protected $casts = [
        'additional_services' => 'array',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}