<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Product\Models\ProductCategory;
use Modules\Support\Http\Requests\Request;

class ProductValidate extends Request
{
    public function rules(): array
    {
        $type = $this->input('type', 'simple');
        $isSimple = $type === 'simple';

        return [
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists(ProductCategory::class, 'id'),
            ],
            'brand_id' => 'nullable|integer|exists:product_brands,id',
            'name' => 'required|string|max:255',
            'price' => $isSimple ? 'required|numeric' : 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'quantity' => $isSimple ? 'required|numeric' : 'nullable|numeric',
            'unit' => 'nullable|string',
            'min_order' => 'nullable|numeric',
            'active' => 'required|boolean',
            'featured' => 'nullable|boolean',
            'type' => 'nullable|in:simple,variable,bundle',
            'is_virtual' => 'nullable|boolean',
            'is_downloadable' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:5120',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,svg|max:5120',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'meta_tag_title' => 'nullable|string|max:60',
            'meta_tag_description' => 'nullable|string|max:160',
        ];
    }
}
