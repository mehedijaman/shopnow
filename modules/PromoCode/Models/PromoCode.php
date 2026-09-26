<?php

namespace Modules\PromoCode\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Order\Models\Order;
use Modules\PromoCode\Database\Factories\PromoCodeFactory;
use Modules\PromoCode\Enums\DiscountType;
use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class PromoCode extends BaseModel
{
    use ActivityLog, HasFactory, Searchable, SoftDeletes;

    protected $table = 'promo_codes';

    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'minimum_order_amount', 'maximum_discount_amount',
        'usage_limit', 'per_customer_limit', 'starts_at', 'expires_at', 'active',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'discount_type' => DiscountType::class,
        'discount_value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    protected static function newFactory(): Factory
    {
        return PromoCodeFactory::new();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }

    public static function normalize(string $code): string
    {
        return strtoupper(trim($code));
    }

    /**
     * Orders (excluding deleted ones) that used this code.
     */
    public function usedCount(bool $lock = false): int
    {
        return $this->orders()
            ->when($lock, fn ($query) => $query->lockForUpdate())
            ->count();
    }

    /**
     * Orders placed by the given customer that used this code.
     */
    public function usedCountForCustomer(?int $customerId, bool $lock = false): int
    {
        if (! $customerId) {
            return 0;
        }

        return $this->orders()
            ->where('customer_id', $customerId)
            ->when($lock, fn ($query) => $query->lockForUpdate())
            ->count();
    }

    public function isStarted(): bool
    {
        return ! $this->starts_at || $this->starts_at->isPast();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasReachedUsageLimit(bool $lock = false): bool
    {
        return $this->usage_limit !== null && $this->usedCount($lock) >= $this->usage_limit;
    }

    public function hasReachedCustomerLimit(?int $customerId, bool $lock = false): bool
    {
        if ($this->per_customer_limit === null) {
            return false;
        }

        return $this->usedCountForCustomer($customerId, $lock) >= $this->per_customer_limit;
    }

    public function meetsMinimumOrder(float $subtotal): bool
    {
        return $subtotal >= (float) $this->minimum_order_amount;
    }

    /**
     * Why this code cannot be applied right now, or null when it is valid.
     * Shared by checkout validation and the cart preview (which detaches stale codes).
     */
    public function validationError(float $subtotal, ?int $customerId = null, bool $lock = false): ?string
    {
        if (! $this->active) {
            return 'This promo code is no longer active.';
        }

        if (! $this->isStarted()) {
            return 'This promo code is not active yet.';
        }

        if ($this->isExpired()) {
            return 'This promo code has expired.';
        }

        if (! $this->meetsMinimumOrder($subtotal)) {
            return sprintf(
                'A minimum order of %s Tk is required to use this promo code.',
                number_format((float) $this->minimum_order_amount, 2),
            );
        }

        if ($this->hasReachedUsageLimit($lock)) {
            return 'This promo code has reached its usage limit.';
        }

        if ($this->hasReachedCustomerLimit($customerId, $lock)) {
            return 'You have already used this promo code the maximum number of times.';
        }

        return null;
    }

    /**
     * Aggregate status shown in the admin list.
     */
    public function status(?int $usedCount = null): string
    {
        if (! $this->active) {
            return 'disabled';
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'scheduled';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        $usedCount ??= $this->usedCount();

        if ($this->usage_limit !== null && $usedCount >= $this->usage_limit) {
            return 'exhausted';
        }

        return 'active';
    }
}
