<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'city',
        'state',
        'postal_code',
        'country',
        'address_1',
        'address_2',
        'contact_number'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}