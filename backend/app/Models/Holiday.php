<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    use HasFactory;

    protected $primaryKey = 'holiday_id';

    public $timestamps = false;

    protected $fillable = [
        'restaurant_id',
        'name',
        'holiday_date',
        'is_closed',
    ];

    protected $casts = [
        'holiday_date' => 'date:Y-m-d',
        'is_closed' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'restaurant_id');
    }
}
