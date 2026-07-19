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
            self::Simple => 'Simple Product',
            self::Variable => 'Variable Product',
            self::Bundle => 'Bundle Product',
        };
    }
}
