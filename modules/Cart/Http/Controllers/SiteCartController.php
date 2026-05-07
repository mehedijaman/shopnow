<?php

namespace Modules\Cart\Http\Controllers;

use Devfaysal\BangladeshGeocode\Models\District;
use Modules\Support\Http\Controllers\SiteController;

class SiteCartController extends SiteController
{
    public function index()
    {
        return view('cart::index');
    }

    public function checkout()
    {
        $districts = District::all();
        $shippingFlatRate = (int) setting('shipping.flat_rate', 60);
        $freeShippingThreshold = (int) setting('shipping.free_shipping_threshold', 1000);

        return view('cart::checkout', compact([
            'districts',
            'shippingFlatRate',
            'freeShippingThreshold',
        ]));
    }
}
