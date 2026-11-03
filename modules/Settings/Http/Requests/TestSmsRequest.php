<?php

namespace Modules\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Settings\Services\SmsSettings;

class TestSmsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('user')?->can('sms-test-send') === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['phone' => SmsSettings::phone((string) $this->input('phone')) ?? $this->input('phone')]);
    }

    public function rules(): array
    {
        return ['phone' => ['required', 'regex:/^01[3-9][0-9]{8}$/D'], 'message' => ['required', 'string', 'max:480']];
    }
}
