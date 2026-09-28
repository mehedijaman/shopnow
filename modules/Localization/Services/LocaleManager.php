<?php

namespace Modules\Localization\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Single source of truth for locale resolution and application.
 *
 * Resolution order (approved): saved locale of the authenticated account for
 * the current guard, then the locale cookie, then the boot locale. Only codes
 * present in config('localization.supported') are ever accepted.
 */
class LocaleManager
{
    /**
     * @param  string  $bootLocale  The configured app locale captured before any request could mutate it.
     */
    public function __construct(private readonly string $bootLocale) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    public function supported(): array
    {
        return config('localization.supported', []);
    }

    /**
     * @return array<int, string>
     */
    public function codes(): array
    {
        return array_keys($this->supported());
    }

    public function isSupported(mixed $locale): bool
    {
        return is_string($locale) && array_key_exists($locale, $this->supported());
    }

    public function bootLocale(): string
    {
        return $this->isSupported($this->bootLocale) ? $this->bootLocale : 'en';
    }

    public function fallbackLocale(): string
    {
        $fallback = config('app.fallback_locale');

        return $this->isSupported($fallback) ? $fallback : $this->bootLocale();
    }

    /**
     * Resolve the locale for the current request.
     */
    public function resolve(Request $request): string
    {
        $account = $this->accountLocale($request);

        if ($account !== null) {
            return $account;
        }

        $cookie = $request->cookie($this->cookieName());

        if ($this->isSupported($cookie)) {
            return $cookie;
        }

        return $this->bootLocale();
    }

    /**
     * Apply a locale to the current process (translator + Carbon).
     */
    public function apply(string $locale): void
    {
        if (! $this->isSupported($locale)) {
            $locale = $this->fallbackLocale();
        }

        app()->setLocale($locale);

        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
    }

    public function cookieName(): string
    {
        return (string) config('localization.cookie.name', 'locale');
    }

    public function localeCookie(string $locale): Cookie
    {
        return cookie(
            name: $this->cookieName(),
            value: $locale,
            minutes: (int) config('localization.cookie.lifetime', 31536000),
            path: '/',
            domain: null,
            secure: request()->secure(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        );
    }

    /**
     * Metadata for every supported locale, keyed by locale code.
     *
     * @return array<string, array<string, mixed>>
     */
    public function meta(): array
    {
        $meta = [];

        foreach ($this->supported() as $code => $config) {
            $meta[$code] = [
                'name' => $config['name'],
                'native' => $config['native'],
                'dir' => $config['dir'],
                'native_digits' => (bool) $config['native_digits'],
            ];
        }

        return $meta;
    }

    public function dir(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        $dir = data_get($this->supported(), $locale.'.dir');

        return is_string($dir) ? $dir : 'ltr';
    }

    public function usesNativeDigits(?string $locale = null): bool
    {
        return (bool) data_get($this->supported(), ($locale ?? app()->getLocale()).'.native_digits', false);
    }

    /**
     * Locale of the account belonging to the guard that owns this request.
     */
    protected function accountLocale(Request $request): ?string
    {
        $user = Auth::guard($this->accountGuard($request))->user();

        if (! is_object($user) || ! method_exists($user, 'preferredLocale')) {
            return null;
        }

        $locale = $user->preferredLocale();

        return $this->isSupported($locale) ? $locale : null;
    }

    protected function accountGuard(Request $request): string
    {
        return $this->isAdminRequest($request) ? 'user' : 'customer';
    }

    public function isAdminRequest(Request $request): bool
    {
        return $request->is('admin') || $request->is('admin/*') || $request->is('admin-auth/*');
    }
}
