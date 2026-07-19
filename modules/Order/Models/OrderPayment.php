<?php

namespace Modules\Order\Models;

use Modules\Support\Models\BaseModel;

class OrderPayment extends BaseModel
{
    protected $table = 'order_payments';

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_status',
        'amount_paid',
        'payment_date',
        'transaction_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
