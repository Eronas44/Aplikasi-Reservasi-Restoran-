<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $primaryKey = 'policy_id';

    protected $fillable = [
        'restaurant_id',
        'deposit_percent',
        'deposit_min_amount',
        'refund_full_hours',
        'refund_partial_hours',
        'refund_partial_percent',
        'is_active',
    ];

    protected $casts = [
        'deposit_percent' => 'decimal:2',
        'deposit_min_amount' => 'decimal:2',
        'refund_partial_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
