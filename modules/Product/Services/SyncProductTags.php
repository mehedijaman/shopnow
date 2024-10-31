<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Product;

class SyncProductTags
{
    public function sync(Product $product, array $tags): void
    {
        if (! count($tags)) {
            $product->tags()->detach();

            return;
        }

        $data = [];
        foreach ($tags as $tag) {
            $data[] = [
                'product_id' => $product->id,
                'product_tag_id' => $tag['id'],
            ];
        }
        $product->tags()->sync($data);
    }
}
