<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'table_id',
        'total',
        'status',
    ];

    public function table()
    {
        return $this->belongsTo(
            Table::class
        );
    }

    public function items()
    {
        return $this->hasMany(
            OrderItem::class
        );
    }

    
}
