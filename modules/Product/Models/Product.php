<?php

namespace Modules\Product\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Product\Database\Factories\ProductFactory;
use Modules\Product\Enums\ProductType;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends BaseModel implements HasMedia
{
    use ActivityLog, HasFactory, InteractsWithMedia, Searchable, Sluggable, SoftDeletes;

    protected $table = 'products';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['image_url'];

    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
        'type' => ProductType::class,
        'is_virtual' => 'boolean',
        'is_downloadable' => 'boolean',
    ];

    public function requiresShipping(): bool
    {
        return ! $this->is_virtual;
    }

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('downloads');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class);
    }

    public function productFiles()
    {
        return $this->hasMany(ProductFile::class)->orderBy('sort_order');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(ProductAttributeValue::class, 'pivot_product_attr_value')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function bundleConfig()
    {
        return $this->hasOne(ProductBundleConfig::class);
    }

    public function bundleItems()
    {
        return $this->hasMany(ProductBundleItem::class, 'bundle_product_id');
    }

    public function partOfBundles()
    {
        return $this->hasMany(ProductBundleItem::class, 'child_product_id');
    }
}
