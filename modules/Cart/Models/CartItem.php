<?php

namespace Modules\Cart\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductVariation;
use Modules\Support\Models\BaseModel;

class CartItem extends BaseModel
{
    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variation_id',
        'quantity',
        'bundle_selection',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'bundle_selection' => 'array',
        'unit_price' => 'float',
        'total_price' => 'float',
    ];

    protected $appends = ['unit_price', 'total_price'];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function unitPrice(): Attribute
    {
        return Attribute::get(function () {
            if ($this->product_variation_id && $this->relationLoaded('productVariation') && $this->productVariation) {
                return $this->productVariation->sale_price
                    ? (float) $this->productVariation->sale_price
                    : (float) $this->productVariation->price;
            }

            if (! $this->relationLoaded('product')) {
                $this->load('product');
            }

            return $this->product->sale_price
                ? (float) $this->product->sale_price
                : (float) $this->product->price;
        });
    }

    public function totalPrice(): Attribute
    {
        return Attribute::get(function () {
            return $this->unit_price * $this->quantity;
        });
    }
}
