<?php

namespace Modules\Product\Listeners;

use Modules\Product\Services\GrantProductDownloads;
use Modules\Support\Events\OrderPaymentConfirmed;

class GrantDownloadsOnPayment
{
    public function __construct(
        private readonly GrantProductDownloads $grantProductDownloads,
    ) {}

    public function handle(OrderPaymentConfirmed $event): void
    {
        $this->grantProductDownloads->run($event);
    }
}
