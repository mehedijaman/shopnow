<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Modules\Customer\Models\Customer;
use Modules\Support\Http\Requests\Request;

class CustomerValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'digits:11',
                Rule::unique(Customer::class)->ignore($this->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(Customer::class)->ignore($this->id),
            ],
            'password' => $this->passwordRules(),
            'confirm_password' => [
                'required_with:password',
                'same:password',
            ],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'active' => 'nullable|boolean',

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
