<?php

namespace Modules\Cart\Http\Controllers;

use Modules\Support\Http\Controllers\SiteController;

class SiteCartController extends SiteController
{
    public function index()
    {
        $shippingFlatRate = (int) setting('shipping.flat_rate', 60);
        $freeShippingThreshold = (int) setting('shipping.free_shipping_threshold', 1000);

        return view('cart::index', compact([
            'shippingFlatRate',
            'freeShippingThreshold',
        ]));
    }

    public function checkout()
    {
        $shippingFlatRate = (int) setting('shipping.flat_rate', 60);
        $freeShippingThreshold = (int) setting('shipping.free_shipping_threshold', 1000);

        return view('cart::checkout', compact([
            'shippingFlatRate',
            'freeShippingThreshold',
        ]));
    }
}
