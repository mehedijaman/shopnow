<?php

namespace Modules\Product\Enums;

enum ProductType: string
{
    case Simple = 'simple';
    case Variable = 'variable';
    case Bundle = 'bundle';

    public function label(): string
    {
        return match ($this) {
            self::Simple => __('product::admin.simple_product'),
            self::Variable => __('product::admin.variable_product'),
            self::Bundle => __('product::admin.bundle_product'),
        };
    }
}
