<?php

namespace Modules\Order\Models;

use Modules\Support\Models\BaseModel;

class OrderShipment extends BaseModel
{
    public $timestamps = false;

    protected $table = 'order_shipments';

    protected $fillable = [
        'order_id',
        'tracking_number',
        'tracking_url',
        'carrier',
        'shopment_status',
        'shipment_date',
        'estimated_delivery',
        'actual_delivery',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
