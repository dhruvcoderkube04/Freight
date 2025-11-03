<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TQLResponse extends Model
{
    use HasFactory;

    protected $table = 'tql_responses'; // Explicitly set table name

    protected $fillable = [
        'quote_id',
        'response',
        'tql_quote_id',
        'status_code',
        'status',
        'error_message'
    ];

    protected $casts = [
        'response' => 'array',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}