<?php

namespace App\Models;

use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    protected $table = 'menus';

    protected $primaryKey = 'menu_id';

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'restaurant_id',
        'item_name',
        'description',
        'price',
        'image_url',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'restaurant_id');
    }

    public function reservationItems(): HasMany
    {
        return $this->hasMany(ReservationItem::class, 'menu_id', 'menu_id');
    }
}
