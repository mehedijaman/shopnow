<?php

namespace Modules\Settings;

use Modules\Support\BaseServiceProvider;

class SettingsServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        include __DIR__.'/helpers.php';
    }
}
