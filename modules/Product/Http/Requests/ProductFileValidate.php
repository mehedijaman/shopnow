<?php

namespace Modules\Product\Http\Requests;

use Modules\Support\Http\Requests\Request;

class ProductFileValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,zip,doc,docx,xls,xlsx,mp3,mp4,jpg,jpeg,png|max:51200',
        ];
    }
}
