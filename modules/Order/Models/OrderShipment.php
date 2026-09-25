<?php

namespace Modules\Order\Models;

use Modules\Order\Enums\ShipmentStatus;
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

    protected $casts = [
        'shopment_status' => ShipmentStatus::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
