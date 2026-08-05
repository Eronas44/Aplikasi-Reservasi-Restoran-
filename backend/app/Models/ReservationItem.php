<?php

namespace App\Models;

use Database\Factories\ReservationItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationItem extends Model
{
    /** @use HasFactory<ReservationItemFactory> */
    use HasFactory;

    protected $primaryKey = 'reservation_item_id';

    public $timestamps = false;

    protected $fillable = [
        'reservation_id',
        'menu_id',
        'quantity',
        'subtotal_price',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_price' => 'decimal:2',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'menu_id');
    }
}
