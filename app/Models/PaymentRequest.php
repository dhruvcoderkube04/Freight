<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $fillable = [
        'user_id', 'quote_id', 'payment_id', 'carrier_data',
        'amount', 'total_amount', 'markup_percent', 'status',
        'admin_note', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'carrier_data' => 'array',
        'approved_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function quote() { return $this->belongsTo(Quote::class); }
    public function payment() { return $this->belongsTo(Payment::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}