<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Customer\Models\Customer;
use Illuminate\Validation\Rules\Password;
use Modules\Support\Http\Requests\Request;

class CustomerAddressValidate extends Request
{
    public function rules(): array
    {
        return [
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'default' => 'nullable|boolean',

        ];
    }

    private function passwordRules()
    {
        $rules = [Password::min(8)];

        if (request()->isMethod('post')) {
            array_unshift($rules, ['required']);
        }

        if (request()->isMethod('put') and request()->isEmptyString('password')) {
            return [];
        }

        return $rules;
    }
}
