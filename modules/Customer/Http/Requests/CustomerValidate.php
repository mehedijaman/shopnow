<?php

namespace Modules\Customer\Http\Requests;

use Modules\Support\Http\Requests\Request;

class CustomerValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
