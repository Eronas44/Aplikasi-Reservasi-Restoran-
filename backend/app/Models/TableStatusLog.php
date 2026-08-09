<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableStatusLog extends Model
{
    use HasFactory;

    protected $table = 'table_status_logs';

    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'table_id',
        'old_status',
        'new_status',
        'changed_by',
        'note',
    ];
}
