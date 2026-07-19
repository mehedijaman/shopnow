<?php

namespace Modules\Product\Models;

use Modules\Customer\Models\Customer;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderProduct;
use Modules\Support\Models\BaseModel;

class DownloadPermission extends BaseModel
{
    protected $table = 'download_permissions';

    protected $fillable = [
        'order_id',
        'order_product_id',
        'customer_id',
        'product_id',
        'product_file_id',
        'download_token',
        'download_limit',
        'download_count',
        'expires_at',
        'first_downloaded_at',
        'last_downloaded_at',
        'active',
    ];

    protected $casts = [
        'download_count' => 'integer',
        'download_limit' => 'integer',
        'active' => 'boolean',
        'expires_at' => 'datetime',
        'first_downloaded_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productFile()
    {
        return $this->belongsTo(ProductFile::class);
    }
}
