<?php

namespace Modules\Page\Http\Requests;

use Modules\Support\Http\Requests\Request;

class PageValidate extends Request
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048', // Max size 2MB
            'meta_tag_title' => 'nullable|string|max:60',
            'meta_tag_description' => 'nullable|string|max:160',
            'published_at' => 'nullable|date',
        ];
    }
}
