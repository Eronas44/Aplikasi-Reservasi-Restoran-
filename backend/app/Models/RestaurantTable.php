<?php

namespace App\Models;

use Database\Factories\RestaurantTableFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    /** @use HasFactory<RestaurantTableFactory> */
    use HasFactory;

    protected $table = 'tables';

    protected $primaryKey = 'table_id';

    public $timestamps = false;

    protected $fillable = [
        'restaurant_id',
        'table_number',
        'capacity',
        'location_area',
        'status',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'table_id', 'table_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'restaurant_id');
    }
}
