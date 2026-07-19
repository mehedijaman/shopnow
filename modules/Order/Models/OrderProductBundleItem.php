<?php

namespace Modules\Order\Models;

use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariation;
use Modules\Support\Models\BaseModel;

class OrderProductBundleItem extends BaseModel
{
    protected $table = 'order_product_bundle_items';

    protected $fillable = [
        'order_product_id', 'product_id', 'product_variation_id',
        'name', 'sku', 'quantity', 'unit_price', 'total_price',
    ];

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
