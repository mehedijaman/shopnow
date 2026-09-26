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
        'consignment_id',
        'courier_status',
        'booked_at',
        'last_synced_at',
        'booking_error',
    ];

    protected $casts = [
        'shopment_status' => ShipmentStatus::class,
        'booked_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
