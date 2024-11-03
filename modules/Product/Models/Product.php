<?php

namespace Modules\Product\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Database\Factories\ProductFactory;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Product extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, Sluggable, SoftDeletes;

    protected $table = 'products';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['image_url'];

    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset("storage/product/{$this->image}");
        }

        return null;
    }

    protected static function newFactory(): Factory
    {
        return ProductFactory::new();
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class);
    }
}
