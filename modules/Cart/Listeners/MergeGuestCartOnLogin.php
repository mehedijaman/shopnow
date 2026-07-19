<?php

namespace Modules\Cart\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cookie;
use Modules\Cart\Models\Cart;

class MergeGuestCartOnLogin
{
    public function handle(Login $event): void
    {
        if ($event->guard !== 'customer') {
            return;
        }

        $customerId = $event->user->getAuthIdentifier();
        $guestToken = request()->cookie('cart_token', request()->header('X-Cart-Token'));

        if (! $guestToken) {
            return;
        }

        $guestCart = Cart::where('guest_token', $guestToken)->first();

        if (! $guestCart) {
            return;
        }

        $customerCart = Cart::firstOrCreate([
            'customer_id' => $customerId,
        ]);

        foreach ($guestCart->items as $guestItem) {
            $existingItem = $customerCart->items()
                ->where('product_id', $guestItem->product_id)
                ->where('product_variation_id', $guestItem->product_variation_id)
                ->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $guestItem->quantity);
            } else {
                $customerCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'product_variation_id' => $guestItem->product_variation_id,
                    'quantity' => $guestItem->quantity,
                    'bundle_selection' => $guestItem->bundle_selection,
                ]);
            }
        }

        $guestCart->items()->delete();
        $guestCart->delete();

        Cookie::queue(Cookie::forget('cart_token'));
    }
}
