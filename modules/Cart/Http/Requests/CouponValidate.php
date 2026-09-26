<?php

namespace Modules\Cart\Http\Requests;

use Modules\Support\Http\Requests\JsonRequest;

class CouponValidate extends JsonRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50',
        ];
    }
}
