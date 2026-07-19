<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductVariation extends BaseModel implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $table = 'product_variations';

    protected $fillable = [
        'product_id', 'sku', 'price', 'sale_price', 'quantity', 'active', 'variation_key',
    ];

    protected $casts = [
        'price' => 'float',
        'sale_price' => 'float',
        'quantity' => 'integer',
        'active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'pivot_var_attr_value', 'product_variation_id', 'product_attribute_value_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}
