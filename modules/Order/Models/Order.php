<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\PaymentStatus;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Order extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'customer_id', 'name', 'email', 'phone', 'division', 'district', 'upazila', 'union', 'address',
        'country', 'status', 'subtotal', 'tax', 'discount', 'shipping', 'shipping_method', 'coupon_code', 'total',
        'paid', 'due', 'payment_status', 'payment_method', 'notes', 'requires_shipping',
        'fraud_checked_at', 'fraud_risk', 'fraud_details',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'fraud_checked_at' => 'datetime',
        'fraud_details' => 'array',
    ];

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function orderPayments()
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    }

    public function orderShipments()
    {
        return $this->hasMany(OrderShipment::class, 'order_id', 'id');
    }
}
