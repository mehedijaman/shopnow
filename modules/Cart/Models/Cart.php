<?php

namespace Modules\Cart\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\Models\Customer;
use Modules\PromoCode\Models\PromoCode;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class Cart extends BaseModel
{
    use ActivityLog, Searchable, SoftDeletes;

    protected $table = 'carts';

    protected $fillable = [
        'customer_id',
        'guest_token',
        'coupon_code',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class, 'coupon_code', 'code');
    }
}
