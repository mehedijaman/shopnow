<?php

namespace Modules\Product\Services;

use Illuminate\Support\Str;
use Modules\Product\Models\ProductTag;

class GetProductTagOptions
{
    public function get(): array
    {
        return ProductTag::orderBy('name')
            ->get()
            ->map(fn ($tag) => [
                'value' => $tag->id,
                'label' => Str::limit($tag->name, 25),
            ])
            ->all();
    }
}
