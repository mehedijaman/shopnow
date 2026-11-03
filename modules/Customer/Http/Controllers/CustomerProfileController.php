<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Settings\Services\SmsSettings;
use Modules\Support\Http\Controllers\AppController;

class CustomerProfileController extends AppController
{
    public function show()
    {
        $customer = Auth::guard('customer')->user();

        return view('customer::profile', compact('customer'));
    }

    public function update(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        if (is_string($request->input('phone'))) {
            $request->merge(['phone' => SmsSettings::phone($request->input('phone')) ?? $request->input('phone')]);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:11|unique:customers,phone,'.$customer->id,
            'email' => 'required|email|max:255|unique:customers,email,'.$customer->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);

        return redirect()->back()->with('success', __('customer::site.profile.updated'));
    }
}
