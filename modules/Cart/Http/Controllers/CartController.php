<?php

namespace Modules\Cart\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Cart\Http\Requests\CartValidate;
use Modules\Cart\Models\Cart;
use Modules\Support\Http\Controllers\BackendController;

class CartController extends BackendController
{
    public function index(): Response
    {
        $carts = Cart::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($cart) => [
                'id' => $cart->id,
                'name' => $cart->name,
                'created_at' => $cart->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Cart/CartIndex', [
            'carts' => $carts,
        ]);
    }

    public function create(): Response
    {
        return inertia('Cart/CartForm');
    }

    public function store(CartValidate $request): RedirectResponse
    {
        Cart::create($request->validated());

        return redirect()->route('cart.index')
            ->with('success', 'Cart created.');
    }

    public function edit(int $id): Response
    {
        $cart = Cart::find($id);

        return inertia('Cart/CartForm', [
            'cart' => $cart,
        ]);
    }

    public function update(CartValidate $request, int $id): RedirectResponse
    {
        $cart = Cart::findOrFail($id);

        $cart->update($request->validated());

        return redirect()->route('cart.index')
            ->with('success', 'Cart updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Cart::findOrFail($id)->delete();

        return redirect()->route('cart.index')
            ->with('success', 'Cart deleted.');
    }
}
