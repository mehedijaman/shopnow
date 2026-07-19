<?php

namespace Modules\Cart\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Http\Requests\CartItemValidate;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Cart\Services\AddItemToCart;
use Modules\Cart\Services\ClearCart;
use Modules\Cart\Services\GetCartTotals;
use Modules\Cart\Services\RemoveCartItem;
use Modules\Cart\Services\UpdateCartItemQuantity;
use Modules\Support\Http\Controllers\SiteController;

class SiteCartController extends SiteController
{
    public function index(GetCartTotals $getCartTotals)
    {
        $cart = app(Cart::class);

        $totals = $getCartTotals->run($cart);

        $shippingFlatRate = (int) setting('shipping.flat_rate', 60);
        $freeShippingThreshold = (int) setting('shipping.free_shipping_threshold', 1000);

        return view('cart::index', compact([
            'totals',
            'shippingFlatRate',
            'freeShippingThreshold',
        ]));
    }

    public function checkout(GetCartTotals $getCartTotals)
    {
        $cart = app(Cart::class);

        $totals = $getCartTotals->run($cart);

        if ($totals['requiresShipping'] === false) {
            $hasDownloadable = collect($totals['items'])->contains(
                fn ($item) => $item['item']['is_downloadable'] ?? false,
            );

            if ($hasDownloadable && ! Auth::guard('customer')->check()) {
                return redirect()->guest(route('customerAuth.loginForm'));
            }
        }

        $shippingFlatRate = (int) setting('shipping.flat_rate', 60);
        $freeShippingThreshold = (int) setting('shipping.free_shipping_threshold', 1000);

        $customer = Auth::guard('customer')->user();
        $addresses = [];
        if ($customer) {
            $addresses = $customer->addresses()
                ->with(['division', 'district', 'upazila', 'union'])
                ->get()
                ->map(fn ($addr) => [
                    'id' => $addr->id,
                    'address' => $addr->address,
                    'division_id' => $addr->division_id,
                    'division_name' => $addr->division?->name,
                    'district_id' => $addr->district_id,
                    'district_name' => $addr->district?->name,
                    'upazilla_id' => $addr->upazilla_id,
                    'upazilla_name' => $addr->upazila?->name,
                    'union_id' => $addr->union_id,
                    'union_name' => $addr->union?->name,
                    'default' => $addr->default,
                ]);
        }

        return view('cart::checkout', compact([
            'totals',
            'shippingFlatRate',
            'freeShippingThreshold',
            'customer',
            'addresses',
        ]));
    }

    public function store(CartItemValidate $request, AddItemToCart $addItemToCart, GetCartTotals $getCartTotals): JsonResponse
    {
        $cart = app(Cart::class);

        $addItemToCart->run($cart, $request->validated());

        $totals = $getCartTotals->run($cart);

        return response()->json($totals);
    }

    public function update(CartItemValidate $request, UpdateCartItemQuantity $updateCartItemQuantity, GetCartTotals $getCartTotals, int $itemId): JsonResponse
    {
        $cart = app(Cart::class);

        $cartItem = CartItem::where('cart_id', $cart->id)->findOrFail($itemId);

        $updateCartItemQuantity->run($cartItem, $request->validated()['quantity']);

        $totals = $getCartTotals->run($cart);

        return response()->json($totals);
    }

    public function destroy(RemoveCartItem $removeCartItem, GetCartTotals $getCartTotals, int $itemId): JsonResponse
    {
        $cart = app(Cart::class);

        $cartItem = CartItem::where('cart_id', $cart->id)->findOrFail($itemId);

        $removeCartItem->run($cartItem);

        $totals = $getCartTotals->run($cart);

        return response()->json($totals);
    }

    public function destroyAll(ClearCart $clearCart, GetCartTotals $getCartTotals): JsonResponse
    {
        $cart = app(Cart::class);

        $clearCart->run($cart);

        $totals = $getCartTotals->run($cart);

        return response()->json($totals);
    }

    public function fetch(GetCartTotals $getCartTotals): JsonResponse
    {
        $cart = app(Cart::class);

        $totals = $getCartTotals->run($cart);

        return response()->json($totals);
    }
}
