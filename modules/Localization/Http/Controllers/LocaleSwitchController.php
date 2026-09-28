<?php

namespace Modules\Localization\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Localization\Services\LocaleManager;

/**
 * POST /locale — switches the UI language and reloads the current page.
 *
 * The redirect target is restricted to the current origin so the endpoint can
 * never be used as an open redirect.
 */
class LocaleSwitchController
{
    public function __construct(private readonly LocaleManager $locales) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in($this->locales->codes())],
        ]);

        $locale = $validated['locale'];

        foreach (['user', 'customer'] as $guard) {
            $account = Auth::guard($guard)->user();

            if (is_object($account) && method_exists($account, 'updatePreferredLocale')) {
                $account->updatePreferredLocale($locale);
            }
        }

        cookie()->queue($this->locales->localeCookie($locale));

        return redirect()->to($this->safeTarget($request));
    }

    /**
     * The page to come back to, but only when it belongs to this origin.
     */
    protected function safeTarget(Request $request): string
    {
        foreach ([$request->input('_redirect'), $request->headers->get('referer')] as $candidate) {
            if (is_string($candidate) && $candidate !== '' && $this->isSameOrigin($request, $candidate)) {
                return $candidate;
            }
        }

        return url('/');
    }

    protected function isSameOrigin(Request $request, string $url): bool
    {
        if (str_contains($url, "\r") || str_contains($url, "\n")) {
            return false;
        }

        $parts = parse_url($url);

        if ($parts === false || isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }

        if (! isset($parts['host'])) {
            return str_starts_with($url, '/') && ! str_starts_with($url, '//');
        }

        if (strcasecmp($parts['host'], $request->getHost()) !== 0) {
            return false;
        }

        return isset($parts['scheme']) ? strtolower($parts['scheme']) === $request->getScheme() : true;
    }
}
