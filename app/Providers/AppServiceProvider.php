<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Settings\Services\SeoService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the exception renderer view namespace unconditionally so
        // `php artisan view:cache` (part of `optimize`) can compile these
        // framework views even when APP_DEBUG is false in production.
        if (! $this->app->hasDebugModeEnabled()) {
            $this->loadViewsFrom(
                base_path('vendor/laravel/framework/src/Illuminate/Foundation/resources/exceptions/renderer'),
                'laravel-exceptions-renderer'
            );
        }

        $this->applyDynamicMailConfig();
        $this->registerSeoViewComposer();
    }

    /**
     * Share default SEO metadata with the site layout so every page that does
     * not override $seo still has sensible fallback meta tags.
     */
    private function registerSeoViewComposer(): void
    {
        View::composer('site-layout', function ($view) {
            if (! $view->offsetExists('seo')) {
                try {
                    $seo = app(SeoService::class)->build();
                    $view->with('seo', $seo);
                } catch (\Throwable) {
                    $view->with('seo', []);
                }
            }
        });
    }

    /**
     * Apply SMTP settings stored in the database at boot time.
     * Wrapped in a try/catch so a fresh install (before migrations) never breaks.
     */
    private function applyDynamicMailConfig(): void
    {
        try {
            $mail = settings_group('mail');

            if (! empty($mail['host'])) {
                config([
                    'mail.mailers.smtp.host' => $mail['host'],
                    'mail.mailers.smtp.port' => $mail['port'] ?? 587,
                    'mail.mailers.smtp.username' => $mail['username'] ?? null,
                    'mail.mailers.smtp.password' => $mail['password'] ?? null,
                    'mail.mailers.smtp.encryption' => $mail['encryption'] ?? 'tls',
                ]);
            }

            if (! empty($mail['from_address'])) {
                config([
                    'mail.from.address' => $mail['from_address'],
                    'mail.from.name' => $mail['from_name'] ?? config('mail.from.name'),
                ]);
            }
        } catch (\Throwable) {
            // Safe on a fresh install before the settings table exists.
        }
    }
}
