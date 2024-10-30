<?php

namespace Modules\Product\Models;

use Carbon\Carbon;
use Modules\Support\Models\BaseModel;
use Modules\Product\Models\ProductTag;
use Modules\Support\Traits\Searchable;
use Modules\Support\Traits\ActivityLog;
use Modules\Product\Models\ProductBrand;
use Cviebrock\EloquentSluggable\Sluggable;
use Modules\Product\Models\ProductCategory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, Sluggable, SoftDeletes;

    protected $table = 'products';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['image_url'];

    // protected $casts = [
    //     'published_at' => 'datetime:Y-m-d',
    // ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
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
