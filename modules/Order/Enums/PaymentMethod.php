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
            self::Cod => __('order::admin.payment_method_cod'),
            self::Card => __('order::admin.payment_method_card'),
            self::Mobile => __('order::admin.payment_method_mobile'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
