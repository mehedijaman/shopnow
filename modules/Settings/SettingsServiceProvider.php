<?php

namespace Modules\Settings;

use Illuminate\Support\Facades\Mail;
use Modules\Support\BaseServiceProvider;

class SettingsServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        include __DIR__.'/helpers.php';

        self::configureMailFromSettings();
    }

    public static function configureMailFromSettings(): void
    {
        try {
            $enableSmtp = (bool) setting('mail.enable_smtp', false);

            if ($enableSmtp) {
                $port = (int) (setting('mail.port') ?: env('MAIL_PORT', 587));
                $encryption = (string) (setting('mail.encryption') ?: env('MAIL_ENCRYPTION', 'tls'));
                $scheme = match (true) {
                    $port === 465 || in_array(strtolower($encryption), ['ssl', 'smtps'], true) => 'smtps',
                    default => 'smtp',
                };

                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.scheme' => $scheme,
                    'mail.mailers.smtp.host' => setting('mail.host') ?: env('MAIL_HOST', '127.0.0.1'),
                    'mail.mailers.smtp.port' => $port,
                    'mail.mailers.smtp.username' => setting('mail.username') ?: env('MAIL_USERNAME'),
                    'mail.mailers.smtp.password' => setting('mail.password') ?: env('MAIL_PASSWORD'),
                    'mail.mailers.smtp.encryption' => $encryption,
                ]);
            } else {
                config([
                    'mail.default' => env('MAIL_MAILER', 'smtp'),
                    'mail.mailers.smtp.host' => env('MAIL_HOST', '127.0.0.1'),
                    'mail.mailers.smtp.port' => (int) env('MAIL_PORT', 2525),
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

            if (class_exists(Mail::class)) {
                Mail::purge('smtp');
            }
        } catch (\Throwable) {
            // Ignore DB errors during early setup/migrations
        }
    }
}
