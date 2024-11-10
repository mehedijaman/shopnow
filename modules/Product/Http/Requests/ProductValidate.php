<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Product\Models\ProductCategory;
use Modules\Support\Http\Requests\Request;

class ProductValidate extends Request
{
    public function rules(): array
    {
        return [
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists(ProductCategory::class, 'id'),
            ],
            // 'brand_id' => 'nullable|exists:blog_authors,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'quantity' => 'required|numeric',
            'unit' => 'nullable|string',
            'min_order' => 'nullable|numeric',
            'active' => 'required|boolean',
            'featured' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'meta_tag_title' => 'nullable|string|max:60',
            'meta_tag_description' => 'nullable|string|max:160',
            // 'published_at' => 'nullable|date',
        ];
    }
}
