<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use HasFactory;

    protected $table = 'waiting_list';

    protected $primaryKey = 'waiting_id';

    public $timestamps = false;

    protected $fillable = [
        'restaurant_id',
        'name',
        'phone',
        'number_of_guest',
        'area',
        'status',
        'assigned_table_id',
        'seated_at',
    ];

    protected $casts = [
        'seated_at' => 'datetime',
    ];
}
