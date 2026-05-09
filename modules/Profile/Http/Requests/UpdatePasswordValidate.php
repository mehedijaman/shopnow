<?php

namespace Modules\Profile\Http\Requests;

use Modules\Support\Http\Requests\Request;

class UpdatePasswordValidate extends Request
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password:user'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
