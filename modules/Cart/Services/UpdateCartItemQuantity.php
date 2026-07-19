<?php

namespace Modules\Cart\Services;

use Modules\Cart\Models\CartItem;

class UpdateCartItemQuantity
{
    public function run(CartItem $cartItem, int $quantity): CartItem
    {
        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->fresh();
    }
}
