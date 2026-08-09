<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $primaryKey = 'restaurant_id';

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'email',
        'rating',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    public function tables(): HasMany
    {
        return $this->hasMany(RestaurantTable::class, 'restaurant_id', 'restaurant_id');
    }

    public function openingHours(): HasMany
    {
        return $this->hasMany(OpeningHour::class, 'restaurant_id', 'restaurant_id');
    }

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class, 'restaurant_id', 'restaurant_id');
    }
}
