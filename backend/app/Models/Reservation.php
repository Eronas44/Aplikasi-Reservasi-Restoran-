<?php

namespace App\Models;

use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    protected $primaryKey = 'reservation_id';

    protected $fillable = [
        'user_id',
        'table_id',
        'booking_code',
        'reservation_date',
        'reservation_time',
        'number_of_guest',
        'status',
        'total_price',
        'deposit_amount',
        'payment_status',
        'staff_id',
        'special_request',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id', 'user_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id', 'table_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class, 'reservation_id', 'reservation_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'reservation_id', 'reservation_id');
    }

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'reservation_time' => 'string',
            'total_price' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
        ];
    }
}
