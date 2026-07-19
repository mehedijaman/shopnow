<?php

namespace Modules\Product\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Modules\Support\Models\BaseModel;

class ProductAttribute extends BaseModel
{
    use Sluggable;

    protected $table = 'product_attributes';

    protected $fillable = [
        'name', 'slug', 'input_type',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class, 'product_attribute_id')->orderBy('sort_order');
    }
}
