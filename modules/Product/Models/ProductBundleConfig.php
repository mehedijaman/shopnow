<?php

namespace Modules\Product\Models;

use Modules\Support\Models\BaseModel;

class ProductBundleConfig extends BaseModel
{
    protected $table = 'product_bundle_configs';

    protected $fillable = [
        'product_id', 'pricing_type', 'discount_type', 'discount_value', 'fixed_price',
    ];

    protected $casts = [
        'discount_value' => 'float',
        'fixed_price' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
