<?php

namespace Modules\Slider;

use Modules\Support\BaseServiceProvider;

class SliderServiceProvider extends BaseServiceProvider
{
    protected ?string $viewNamespace = 'slider';

    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'slider');
    }
}
