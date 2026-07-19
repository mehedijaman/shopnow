<?php

namespace Modules\Cart\Services;

use Modules\Cart\Models\CartItem;

class RemoveCartItem
{
    public function run(CartItem $cartItem): void
    {
        $cartItem->delete();
    }
}
