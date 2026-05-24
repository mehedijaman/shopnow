<?php

namespace Modules\Slider\Http\Requests;

use Modules\Support\Http\Requests\Request;

class SliderValidate extends Request
{
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'url' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ];
    }
}
