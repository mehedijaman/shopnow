<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Order extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'customer_id', 'name', 'email', 'phone', 'division', 'district', 'upazila', 'union', 'address',
        'country', 'status', 'subtotal', 'tax', 'shipping', 'total', 'paid', 'due',
        'payment_status', 'payment_method', 'notes', 'requires_shipping', 'created_by', 'updated_by', 'deleted_by',
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
