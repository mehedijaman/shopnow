<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Support\Models\BaseModel;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductFile extends BaseModel
{
    use SoftDeletes;

    protected $table = 'product_files';

    protected $fillable = [
        'product_id',
        'media_id',
        'name',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
