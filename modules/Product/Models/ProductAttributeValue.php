<?php

namespace Modules\Product\Models;

use Modules\Support\Models\BaseModel;

class ProductAttributeValue extends BaseModel
{
    protected $table = 'product_attribute_values';

    protected $fillable = [
        'product_attribute_id', 'value', 'slug', 'swatch', 'sort_order',
    ];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'pivot_product_attr_value', 'product_attribute_value_id', 'product_id');
    }

    public function variations()
    {
        return $this->belongsToMany(ProductVariation::class, 'pivot_var_attr_value', 'product_attribute_value_id', 'product_variation_id');
    }
}
