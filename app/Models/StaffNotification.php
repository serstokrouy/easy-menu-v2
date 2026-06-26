<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Table;
use App\Models\Order;

class StaffNotification extends Model
{
    protected $fillable = [
        'table_id',
        'order_id',
        'message',
        'status',
        'audio_path',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
