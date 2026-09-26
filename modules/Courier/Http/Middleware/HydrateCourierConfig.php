<?php

namespace Modules\Courier\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Courier\Services\CourierConfigHydrator;

/**
 * Attached to the CourierHub webhook route so courier credentials and webhook
 * secrets are loaded from settings before the package driver validates them.
 */
class HydrateCourierConfig
{
    public function __construct(private CourierConfigHydrator $hydrator) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->hydrator->hydrate();

        return $next($request);
    }
}
