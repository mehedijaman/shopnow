<?php

namespace Modules\Product\Http\Requests;

use Modules\Support\Http\Requests\Request;

class ProductTagValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
