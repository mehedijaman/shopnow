<?php

namespace Modules\Product\Models;

use Modules\Support\Models\BaseModel;

class ProductBundleItem extends BaseModel
{
    protected $table = 'product_bundle_items';

    protected $fillable = [
        'bundle_product_id', 'child_product_id', 'child_product_variation_id',
        'quantity', 'is_optional', 'price_override', 'sort_order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'is_optional' => 'boolean',
        'price_override' => 'float',
        'sort_order' => 'integer',
    ];

    public function bundleProduct()
    {
        return $this->belongsTo(Product::class, 'bundle_product_id');
    }

    public function childProduct()
    {
        return $this->belongsTo(Product::class, 'child_product_id');
    }

    public function childProductVariation()
    {
        return $this->belongsTo(ProductVariation::class, 'child_product_variation_id');
    }
}
