<?php

namespace Modules\Product\Observers;

use Illuminate\Support\Str;
use Modules\Product\Models\Product;

class ProductObserver
{
    public function creating(Product $product): void
    {
        $this->setMetaTagTitle($product);
        $this->setMetaTagDescription($product);
    }

    private function setMetaTagTitle(Product $product): void
    {
        if (! request()->has('meta_tag_title') or empty(request('meta_tag_title'))) {
            $product->meta_tag_title = Str::limit($product->name, 60, '');
        }
    }

    private function setMetaTagDescription(Product $product): void
    {
        if (! request()->has('meta_tag_description') or empty(request('meta_tag_description'))) {
            $description = strip_tags((string) $product->description);

            // Add a space after punctuation, with exceptions for digits and ellipsis
            $pattern = [
                '/(\.\.\.)(?=[^\s])/u',  // Match ellipsis not followed by a space
                '/(?<!\.\.)(?<!\d)([.!?])(?=[^\s.!?])/u',  // Match punctuation not preceded by digit/ellipsis and not followed by space/punctuation
            ];

            $replace = [
                '$1 ',  // Add a space after ellipsis
                '$1 ',  // Add a space after punctuation
            ];

            $description = preg_replace($pattern, $replace, $description);

            $product->meta_tag_description = Str::limit($description, 160, '');
        }
    }
}
