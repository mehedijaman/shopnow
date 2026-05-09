<?php

namespace Modules\Profile\Http\Requests;

use Modules\Support\Http\Requests\Request;

class ProfileValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
