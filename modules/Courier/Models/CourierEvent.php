<?php

namespace Modules\Courier\Models;

use Modules\Order\Models\Order;
use Modules\Support\Models\BaseModel;

class CourierEvent extends BaseModel
{
    public const UPDATED_AT = null;

    protected $table = 'courier_events';

    protected $fillable = [
        'courier',
        'order_id',
        'tracking_id',
        'status',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
