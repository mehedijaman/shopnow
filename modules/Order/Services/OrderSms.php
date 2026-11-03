<?php

namespace Modules\Order\Services;

use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Settings\Services\SmsMessages;
use Modules\Settings\Services\SmsSettings;

class OrderSms
{
    public function __construct(private SmsSettings $settings, private SmsMessages $messages) {}

    public function placed(Order $order): void
    {
        $this->record($order, 'order_placed', 'order:'.$order->id.':placed');
    }

    public function updated(Order $order): void
    {
        $this->record($order, 'order_updated', 'order:'.$order->id.':status:'.$order->sms_status_version);
    }

    private function record(Order $order, string $purpose, string $eventKey): void
    {
        $settings = $this->settings->values();
        $enabled = $settings[$purpose === 'order_placed' ? 'order_placed' : 'order_updated'];
        if (! $settings['enabled'] || ! $enabled) {
            return;
        }
        $status = OrderStatus::tryFrom((string) ($order->getAttributes()['status'] ?? 'pending')) ?? OrderStatus::Pending;
        if ($purpose === 'order_updated' && ! in_array($status->value, $settings['statuses'], true)) {
            return;
        }
        $phone = SmsSettings::phone((string) $order->phone);
        if ($phone === null) {
            return;
        }
        $locale = $order->locale === 'bn' ? 'bn' : 'en';
        $statusTranslationKey = "order::enums.order_status.{$status->value}";
        $template = $purpose === 'order_placed' ? 'placement_'.$locale : 'status_'.$status->value.'_'.$locale;
        $body = $this->settings->render($settings[$template], [
            'customer_name' => $order->name, 'order_number' => $order->id,
            'status' => trans($statusTranslationKey, [], $locale),
            'total' => number_format((float) $order->total, 2, '.', ''),
        ]);
        $this->messages->create($eventKey, $purpose, $phone, $body, ['order_id' => $order->id, 'customer_id' => $order->customer_id, 'order_status' => $status->value]);
    }
}
