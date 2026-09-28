<?php

namespace Modules\Localization\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Localization\Services\LocaleManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves and applies the locale for every web request.
 *
 * Must be appended to the "web" group before HandleInertiaRequests so the
 * shared Inertia props and <html lang dir> reflect the resolved locale.
 */
class SetLocale
{
    public function __construct(private readonly LocaleManager $locales) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->locales->apply($this->locales->resolve($request));

        $response = $next($request);

        $response->headers->set('Content-Language', app()->getLocale());

        return $response;
    }
}
