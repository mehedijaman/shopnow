<?php

namespace Modules\Order\Enums;

enum PaymentMethod: string
{
    case Cod = 'cod';
    case Card = 'card';
    case Mobile = 'mobile';

    public function label(): string
    {
        return match ($this) {
            self::Cod => 'Cash on Delivery',
            self::Card => 'Card',
            self::Mobile => 'Mobile Payment',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
