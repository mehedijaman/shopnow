<?php

namespace Modules\Cart\Http\Controllers;

use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
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
        return view('cart::checkout', compact([
            'districts',
        ]));
    }
}
