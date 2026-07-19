<?php

namespace Modules\Cart\Services;

use Modules\Cart\Models\Cart;

class ClearCart
{
    public function run(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
