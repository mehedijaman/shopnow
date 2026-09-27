<?php

namespace Modules\Courier\Services\Fraud\Contracts;

interface FraudProviderInterface
{
    /**
     * Stable payload key used in fraud_details and provider lookups.
     */
    public function name(): string;

    /**
     * Whether the source is enabled from the courier fraud settings.
     */
    public function enabled(): bool;

    /**
     * Whether the credentials required to call the source are present.
     */
    public function configured(): bool;

    /**
     * Queries the source for the given normalized BD mobile number.
     *
     * @return array<string, mixed> normalized stats (success/cancel/total/
     *                              success_ratio plus source extras) or an
     *                              entry containing an 'error' key
     */
    public function check(string $phone): array;
}
