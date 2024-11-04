<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Customer\Http\Requests\CustomerValidate;
use Modules\Customer\Models\Customer;
use Modules\Support\Http\Controllers\BackendController;

class CustomerController extends BackendController
{
    public function index(): Response
    {
        $customers = Customer::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'created_at' => $customer->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('Customer/CustomerIndex', [
            'customers' => $customers,
        ]);
    }

    public function create(): Response
    {
        return inertia('Customer/CustomerForm');
    }

    public function store(CustomerValidate $request): RedirectResponse
    {
        Customer::create($request->validated());

        return redirect()->route('customer.index')
            ->with('success', 'Customer created.');
    }

    public function edit(int $id): Response
    {
        $customer = Customer::find($id);

        return inertia('Customer/CustomerForm', [
            'customer' => $customer,
        ]);
    }

    public function update(CustomerValidate $request, int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);

        $customer->update($request->validated());

        return redirect()->route('customer.index')
            ->with('success', 'Customer updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Customer::findOrFail($id)->delete();

        return redirect()->route('customer.index')
            ->with('success', 'Customer deleted.');
    }
}
