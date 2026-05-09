<?php

namespace Modules\Profile\Http\Requests;

use Modules\Support\Http\Requests\Request;

class UpdateProfileValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
