<?php

namespace Modules\Index\Http\Controllers;

use Modules\Support\Http\Controllers\BackendController;
use Modules\Index\Http\Requests\ShoppingCartValidate;
use Modules\Index\Models\ShoppingCart;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\SiteController;

class ShoppingCartController extends SiteController
{
    public function index()
    {
        return view('shopping-cart');
    }

    public function create(): Response
    {
        return inertia('Index/ShoppingCartForm');
    }

    public function store(ShoppingCartValidate $request): RedirectResponse
    {
        ShoppingCart::create($request->validated());

        return redirect()->route('shoppingCart.index')
            ->with('success', 'ShoppingCart created.');
    }

    public function edit(int $id): Response
    {
        $shoppingCart = ShoppingCart::find($id);

        return inertia('Index/ShoppingCartForm', [
            'shoppingCart' => $shoppingCart
        ]);
    }

    public function update(ShoppingCartValidate $request, int $id): RedirectResponse
    {
        $shoppingCart = ShoppingCart::findOrFail($id);

        $shoppingCart->update($request->validated());

        return redirect()->route('shoppingCart.index')
            ->with('success', 'ShoppingCart updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        ShoppingCart::findOrFail($id)->delete();

        return redirect()->route('shoppingCart.index')
            ->with('success', 'ShoppingCart deleted.');
    }
}
