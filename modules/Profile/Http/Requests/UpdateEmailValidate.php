<?php

namespace Modules\Profile\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Modules\Support\Http\Requests\Request;

class UpdateEmailValidate extends Request
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'current_password' => ['required', 'current_password:user'],
        ];
    }
}
