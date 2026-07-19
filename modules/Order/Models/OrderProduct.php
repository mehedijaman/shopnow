<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariation;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class OrderProduct extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'order_products';

    protected $fillable = [
        'order_id', 'product_id', 'product_variation_id', 'variation_label',
        'quantity', 'unit_price', 'discount', 'total_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class)->withTrashed();
    }

    public function bundleItems()
    {
        return $this->hasMany(OrderProductBundleItem::class);
    }
}
