<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Customer\Http\Requests\CustomerValidate;
use Modules\Customer\Models\Customer;
use Modules\Support\Http\Controllers\BackendController;

class CustomerController extends BackendController
{
    public function index(Request $request): Response
    {
        $activeFilter = $request->input('active');

        $customers = Customer::orderByDesc('id')
            ->search($request->input('searchContext'), $request->input('searchTerm'))
            ->when($activeFilter !== null && $activeFilter !== '', fn ($q) => $q->where('active', (bool) $activeFilter))
            ->paginate($request->input('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'email_verified_at' => $customer->email_verified_at,
                'active' => $customer->active,
                'gender' => $customer->gender,
                'date_of_birth' => $customer->date_of_birth?->format('d M Y'),
                'created_at' => $customer->created_at->format('d M Y'),
            ]);

        return inertia('Customer/CustomerIndex', [
            'customers' => $customers,
            'filters' => [
                'active' => $activeFilter,
                'searchTerm' => $request->input('searchTerm'),
            ],
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
        $customer = Customer::findOrFail($id);

        return inertia('Customer/CustomerForm', [
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'active' => $customer->active,
                'gender' => $customer->gender,
                'date_of_birth' => $customer->date_of_birth?->format('Y-m-d'),
            ],
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
                'name' => $customer->name,
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
