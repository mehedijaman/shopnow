<?php

namespace Modules\Order\Models;

use Modules\Product\Models\Product;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class OrderProduct extends BaseModel
{
    use ActivityLog, Searchable;

    protected $table = 'order_products';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
