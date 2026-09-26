<?php

namespace Modules\Courier;

use CourierHub\Events\CourierWebhookReceived;
use Illuminate\Support\Facades\Event;
use Modules\Courier\Console\CourierPollCommand;
use Modules\Courier\Http\Middleware\HydrateCourierConfig;
use Modules\Courier\Listeners\HandleCourierWebhook;
use Modules\Support\BaseServiceProvider;

class CourierServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        config([
            'courierhub.webhook.middleware' => [HydrateCourierConfig::class],
        ]);
    }

    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'courier');

        $this->commands([CourierPollCommand::class]);

        Event::listen(CourierWebhookReceived::class, HandleCourierWebhook::class);
    }
}
