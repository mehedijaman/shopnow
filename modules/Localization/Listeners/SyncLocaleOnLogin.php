<?php

namespace Modules\Localization\Listeners;

use Illuminate\Auth\Events\Login;
use Modules\Localization\Services\LocaleManager;

/**
 * Merges the locale preference at login time.
 *
 * An explicit cookie choice wins and is saved to the account; otherwise the
 * account's saved locale is written back to the cookie.
 */
class SyncLocaleOnLogin
{
    public function __construct(private readonly LocaleManager $locales) {}

    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! is_object($user) || ! method_exists($user, 'preferredLocale')) {
            return;
        }

        $cookie = request()->cookie($this->locales->cookieName());

        if ($this->locales->isSupported($cookie)) {
            $user->updatePreferredLocale($cookie);

            return;
        }

        $accountLocale = $user->preferredLocale();

        if ($accountLocale !== null) {
            cookie()->queue($this->locales->localeCookie($accountLocale));
        }
    }
}
