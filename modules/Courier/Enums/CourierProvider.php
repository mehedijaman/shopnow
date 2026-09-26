<?php

namespace Modules\Courier\Enums;

enum CourierProvider: string
{
    case Pathao = 'pathao';
    case Steadfast = 'steadfast';
    case Redx = 'redx';
    case Ecourier = 'ecourier';
    case Paperfly = 'paperfly';

    public function label(): string
    {
        return match ($this) {
            self::Pathao => 'Pathao',
            self::Steadfast => 'Steadfast',
            self::Redx => 'RedX',
            self::Ecourier => 'eCourier',
            self::Paperfly => 'Paperfly',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
