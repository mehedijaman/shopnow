<?php

namespace Modules\Localization;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Localization\Console\LocalizationCheckCommand;
use Modules\Localization\Console\LocalizationExportCommand;
use Modules\Localization\Listeners\SyncLocaleOnLogin;
use Modules\Localization\Services\LocaleManager;
use Modules\Localization\Support\MissingTranslationKeys;
use Modules\Support\BaseServiceProvider;

class LocalizationServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        parent::register();

        include __DIR__.'/helpers.php';

        $this->app->singleton(MissingTranslationKeys::class, fn () => new MissingTranslationKeys);

        // Bound eagerly: the boot locale must be captured before any request
        // has a chance to mutate config('app.locale') via App::setLocale().
        $this->app->instance(
            LocaleManager::class,
            new LocaleManager((string) $this->app['config']->get('app.locale', 'en'))
        );
    }

    public function boot(): void
    {
        parent::boot();

        $this->registerMissingKeyRecorder();
        $this->registerConsoleCommands();

        Event::listen(Login::class, SyncLocaleOnLogin::class);
    }

    protected function registerMissingKeyRecorder(): void
    {
        $recorder = $this->app->make(MissingTranslationKeys::class);

        $this->app->make('translator')->handleMissingKeysUsing(
            function ($key, $replace, $locale) use ($recorder) {
                $recorded = $recorder->record((string) $key, (string) $locale, is_array($replace) ? $replace : []);

                if ($recorded && $this->app->environment('local')) {
                    Log::warning('Missing translation key', ['key' => $key, 'locale' => $locale]);
                }

                return $key;
            }
        );
    }

    protected function registerConsoleCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            LocalizationExportCommand::class,
            LocalizationCheckCommand::class,
        ]);
    }
}
