<?php

namespace Modules\Cart\Http\Controllers;

use Modules\Support\Http\Controllers\SiteController;

class SiteCartController extends SiteController
{
    public function index()
    {
        return view('cart::index');
    }
}
