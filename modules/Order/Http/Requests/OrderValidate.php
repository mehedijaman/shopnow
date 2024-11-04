<?php

namespace Modules\Order\Http\Requests;

use Modules\Support\Http\Requests\Request;

class OrderValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
