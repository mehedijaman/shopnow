<?php

namespace Modules\Courier;

use CourierHub\Events\CourierWebhookReceived;
use CourierHub\Facades\Courier;
use Illuminate\Support\Facades\Event;
use Modules\Courier\Console\CourierPollCommand;
use Modules\Courier\Http\Middleware\HydrateCourierConfig;
use Modules\Courier\Listeners\HandleCourierWebhook;
use Modules\Courier\Services\Steadfast\SteadfastDriver;
use Modules\Support\BaseServiceProvider;

class CourierServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        parent::register();

        config([
            'courierhub.webhook.middleware' => [HydrateCourierConfig::class],
            // The package ships the retired portal.steadfast.com.bd host;
            // portal.packzy.com is the live API root (verified via /ping).
            'courierhub.couriers.steadfast.base_url' => 'https://portal.packzy.com/api/v1',
            // SteadFast locks the API key for 60 minutes after 10 auth
            // failures in 5 minutes. Laravel retries every non-2xx response
            // by default, so HTTP-level retries would burn that budget on a
            // single bad key; the booking job's own backoff handles blips.
            'courierhub.http.retry' => 1,
        ]);
    }

    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'courier');

        $this->commands([CourierPollCommand::class]);

        Event::listen(CourierWebhookReceived::class, HandleCourierWebhook::class);

        Courier::extend('steadfast', fn () => new SteadfastDriver(
            (array) config('courierhub.couriers.steadfast'),
            (array) config('courierhub.http'),
        ));
    }
}
