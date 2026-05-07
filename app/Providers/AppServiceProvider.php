<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        $this->applyDynamicMailConfig();
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
