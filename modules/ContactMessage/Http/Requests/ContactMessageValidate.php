<?php

namespace Modules\ContactMessage\Http\Requests;

use Modules\Support\Http\Requests\Request;

class ContactMessageValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ];
    }
}
