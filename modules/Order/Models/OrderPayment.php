<?php

namespace Modules\Order\Models;

use Modules\Order\Enums\PaymentMethod;
use Modules\Order\Enums\TransactionStatus;
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

    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'payment_status' => TransactionStatus::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
