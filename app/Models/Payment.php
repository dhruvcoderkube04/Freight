<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quote_id',
        'user_id',
        // Carrier details
        'carrier_name',
        'carrier_scac',
        'is_preferred',
        'is_carrier_of_the_year',
        'customer_rate',
        'transit_days',
        'service_level',
        'service_type',
        'max_liability_new',
        'max_liability_used',
        'price_charges',
        // Payment info
        'payment_status',
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'stripe_charge_id',
        // Admin approval
        'requires_approval',
        'approval_reason',
        'approved_by',
        'approved_at',
        'markup_percent',
        // Payment amounts
        'currency',
        'amount',
        'tax_amount',
        'total_amount',
    ];

    protected $casts = [
        'is_preferred' => 'boolean',
        'is_carrier_of_the_year' => 'boolean',
        'customer_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'max_liability_new' => 'decimal:2',
        'max_liability_used' => 'decimal:2',
        'requires_approval' => 'boolean',
        'approved_at' => 'datetime',
        'price_charges' => 'array',
    ];

    // Relationships
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true)->where('payment_status', 'requires_approval');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    // Methods
    public function requiresAdminApproval()
    {
        return $this->requires_approval && $this->payment_status === 'requires_approval';
    }

    public function isApproved()
    {
        return !is_null($this->approved_at);
    }

    public function canProcessPayment()
    {
        return $this->payment_status === 'approved' || 
               (!$this->requires_approval && $this->payment_status === 'pending');
    }

    // Accessors for formatted display
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->customer_rate, 2);
    }

    public function getCarrierSummaryAttribute()
    {
        return "{$this->carrier_name} ({$this->carrier_scac}) - {$this->service_level}";
    }

    // Accessor for user billing info
    public function getBillingInfoAttribute()
    {
        return [
            'name' => $this->user->name,
            'email' => $this->user->email,
            // Add more user fields if available in your User model
        ];
    }
}