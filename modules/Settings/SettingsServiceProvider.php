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

        $this->configureMailFromSettings();
    }

    private function configureMailFromSettings(): void
    {
        try {
            $enableSmtp = (bool) setting('mail.enable_smtp', false);

            if ($enableSmtp) {
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => setting('mail.host') ?: env('MAIL_HOST', '127.0.0.1'),
                    'mail.mailers.smtp.port' => setting('mail.port') ?: env('MAIL_PORT', 587),
                    'mail.mailers.smtp.username' => setting('mail.username') ?: env('MAIL_USERNAME'),
                    'mail.mailers.smtp.password' => setting('mail.password') ?: env('MAIL_PASSWORD'),
                    'mail.mailers.smtp.encryption' => setting('mail.encryption') ?: env('MAIL_ENCRYPTION', 'tls'),
                ]);
            } else {
                config([
                    'mail.default' => env('MAIL_MAILER', 'smtp'),
                    'mail.mailers.smtp.host' => env('MAIL_HOST', '127.0.0.1'),
                    'mail.mailers.smtp.port' => env('MAIL_PORT', 2525),
                    'mail.mailers.smtp.username' => env('MAIL_USERNAME'),
                    'mail.mailers.smtp.password' => env('MAIL_PASSWORD'),
                    'mail.mailers.smtp.encryption' => env('MAIL_ENCRYPTION', 'tls'),
                ]);
            }

            $fromAddress = setting('mail.from_address') ?: env('MAIL_FROM_ADDRESS', 'hello@example.com');
            $fromName = setting('mail.from_name') ?: env('MAIL_FROM_NAME', setting('branding.site_name', config('app.name')));

            config([
                'mail.from.address' => $fromAddress,
                'mail.from.name' => $fromName,
            ]);
        } catch (\Throwable) {
            // Ignore DB errors during early setup/migrations
        }
    }
}
