<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Customer\Models\CustomerAddress;
use Modules\Support\Http\Controllers\AppController;

class CustomerAddressController extends AppController
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $addresses = $customer->addresses()->with(['division', 'district', 'upazila', 'union'])->get();

        return view('customer::addresses.index', compact('customer', 'addresses'));
    }

    public function create()
    {
        $customer = Auth::guard('customer')->user();

        return view('customer::addresses.create', compact('customer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'upazilla_id' => 'required|exists:upazilas,id',
            'union_id' => 'required|exists:unions,id',
            'address' => 'required|string|max:255',
            'default' => 'nullable|boolean',
        ]);

        $default = $request->boolean('default');

        // If this is the customer's first address, force it to be default
        if ($customer->addresses()->count() === 0) {
            $default = true;
        }

        if ($default) {
            $customer->addresses()->update(['default' => false]);
        }

        $customer->addresses()->create([
            'name' => $validated['name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'division_id' => $validated['division_id'],
            'district_id' => $validated['district_id'],
            'upazilla_id' => $validated['upazilla_id'],
            'union_id' => $validated['union_id'],
            'address' => $validated['address'],
            'country' => 'Bangladesh',
            'default' => $default,
        ]);

        return redirect()->route('account.addresses.index')->with('success', 'Address added successfully.');
    }

    public function edit(CustomerAddress $address)
    {
        $customer = Auth::guard('customer')->user();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        return view('customer::addresses.edit', compact('customer', 'address'));
    }

    public function update(Request $request, CustomerAddress $address): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'upazilla_id' => 'required|exists:upazilas,id',
            'union_id' => 'required|exists:unions,id',
            'address' => 'required|string|max:255',
            'default' => 'nullable|boolean',
        ]);

        $default = $request->boolean('default');

        if ($default) {
            $customer->addresses()->where('id', '!=', $address->id)->update(['default' => false]);
        }

        $address->update([
            'name' => $validated['name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'division_id' => $validated['division_id'],
            'district_id' => $validated['district_id'],
            'upazilla_id' => $validated['upazilla_id'],
            'union_id' => $validated['union_id'],
            'address' => $validated['address'],
            'default' => $default,
        ]);

        return redirect()->route('account.addresses.index')->with('success', 'Address updated successfully.');
    }

    public function destroy(CustomerAddress $address): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $wasDefault = $address->default;
        $address->delete();

        // If the deleted address was default, set another one as default
        if ($wasDefault) {
            $nextAddress = $customer->addresses()->first();
            if ($nextAddress) {
                $nextAddress->update(['default' => true]);
            }
        }

        return redirect()->route('account.addresses.index')->with('success', 'Address deleted successfully.');
    }

    public function makeDefault(CustomerAddress $address): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $customer->addresses()->update(['default' => false]);
        $address->update(['default' => true]);

        return redirect()->route('account.addresses.index')->with('success', 'Default address updated.');
    }
}
