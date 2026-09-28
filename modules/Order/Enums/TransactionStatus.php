<?php

namespace Modules\Order\Enums;

enum TransactionStatus: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('order::enums.transaction_status.pending'),
            self::Success => __('order::enums.transaction_status.success'),
            self::Failed => __('order::enums.transaction_status.failed'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isSuccess(): bool
    {
        return $this === self::Success;
    }
}
