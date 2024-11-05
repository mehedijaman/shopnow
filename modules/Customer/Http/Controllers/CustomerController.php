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
        $customers = Customer::orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($customer) => [
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'email_verified_at' => $customer->email_verified_at,
                'active' => $customer->active,
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
        $customer = Customer::create($request->validated());

        if ($request->input('addresses')) {
            foreach ($request->input('addresses') as $address) {
                $customer->addresses()->create($address);
            }
        }

        return redirect()->route('customer.index')
            ->with('success', 'Customer Created.');
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

    public function recycleBin(): Response
    {
        $customers = Customer::onlyTrashed()
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($customer) => [
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'email_verified_at' => $customer->email_verified_at,
                'active' => $customer->active,
            ]);

        return inertia('Customer/CustomerRecycleBin', [
            'customers' => $customers,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Customer::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('customer.recycleBin.index')
            ->with('success', 'Customer restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $customer = Customer::onlyTrashed()->findOrFail($id);

        $customer->forceDelete();

        return redirect()->route('customer.recycleBin.index')->with('success', 'Customer deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $customers = Customer::onlyTrashed()->get();

        foreach ($customers as $customer) {
            $customer->forceDelete();
        }

        return redirect()->route('customer.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Customer::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('customer.recycleBin.index')
            ->with('success', 'Customer restored.');
    }
}
