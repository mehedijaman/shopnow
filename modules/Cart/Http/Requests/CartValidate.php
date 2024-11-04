<?php

namespace Modules\Cart\Http\Requests;

use Modules\Support\Http\Requests\Request;

class CartValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
