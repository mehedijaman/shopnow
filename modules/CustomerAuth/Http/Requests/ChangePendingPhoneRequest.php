<?php

namespace Modules\CustomerAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Customer\Models\Customer;
use Modules\Settings\Services\SmsSettings;

class ChangePendingPhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->has('pending_customer_id');
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('phone'))) {
            $this->merge(['phone' => SmsSettings::phone($this->input('phone')) ?? $this->input('phone')]);
        }
    }

    public function rules(): array
    {
        return ['phone' => [
            'required', 'string', 'regex:/^01[3-9][0-9]{8}$/D',
            Rule::unique(Customer::class)->ignore($this->session()->get('pending_customer_id')),
        ]];
    }
}
