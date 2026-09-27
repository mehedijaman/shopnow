<?php

namespace Modules\Courier\Services;

use Illuminate\Support\Facades\Log;
use Modules\Courier\Services\Fraud\Contracts\FraudProviderInterface;
use Modules\Courier\Services\Fraud\Providers\BdCourierFraudProvider;
use Modules\Courier\Services\Fraud\Providers\SteadfastFraudProvider;

/**
 * Aggregates COD cancel history from the configured fraud sources.
 *
 * Each source implements FraudProviderInterface: disabled or unconfigured
 * sources are reported as an error entry and excluded from the aggregate,
 * so the payload shape is always the same. Risk is decided per source by
 * the CheckOrderFraud job — one clean source must never mask a bad one.
 */
class FraudChecker
{
    /**
     * @return array<string, mixed> per-source stats plus an aggregate summary
     */
    public function check(string $phone): array
    {
        $payload = [
            'steadfast' => null,
            'bdcourier' => null,
            'aggregate' => [
                'total_success' => 0,
                'total_cancel' => 0,
                'total_deliveries' => 0,
                'success_ratio' => 0,
                'cancel_ratio' => 0,
            ],
        ];

        $totalSuccess = 0;
        $totalCancel = 0;

        foreach ($this->providers() as $provider) {
            $stats = $this->runProvider($provider, $phone);
            $payload[$provider->name()] = $stats;

            if (isset($stats['success'], $stats['cancel'])
                && is_numeric($stats['success'])
                && is_numeric($stats['cancel'])) {
                $totalSuccess += (int) $stats['success'];
                $totalCancel += (int) $stats['cancel'];
            }
        }

        $total = $totalSuccess + $totalCancel;

        $payload['aggregate']['total_success'] = $totalSuccess;
        $payload['aggregate']['total_cancel'] = $totalCancel;
        $payload['aggregate']['total_deliveries'] = $total;

        if ($total > 0) {
            $payload['aggregate']['success_ratio'] = round(($totalSuccess / $total) * 100, 2);
            $payload['aggregate']['cancel_ratio'] = round(($totalCancel / $total) * 100, 2);
        }

        return $payload;
    }

    /**
     * Per-source stats that actually answered (enabled, configured, reachable).
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, array<string, mixed>>
     */
    public static function answeredStats(array $payload): array
    {
        return collect($payload)
            ->except('aggregate')
            ->filter(fn ($value) => is_array($value) && ! array_key_exists('error', $value))
            ->all();
    }

    /**
     * Number of sources that actually answered with stats.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function answeredProviders(array $payload): int
    {
        return count(self::answeredStats($payload));
    }

    /**
     * @return array<int, FraudProviderInterface>
     */
    private function providers(): array
    {
        return [
            new SteadfastFraudProvider,
            new BdCourierFraudProvider,
        ];
    }

    /**
     * @return array<string, mixed> normalized stats or an error entry
     */
    private function runProvider(FraudProviderInterface $provider, string $phone): array
    {
        if (! $provider->enabled()) {
            return ['error' => 'Disabled'];
        }

        if (! $provider->configured()) {
            return ['error' => 'Not configured'];
        }

        try {
            return $provider->check($phone);
        } catch (\Throwable $e) {
            Log::error("FraudChecker: {$provider->name()} source failed.", [
                'message' => $e->getMessage(),
                'phone' => $phone,
            ]);

            return [
                'error' => 'Service unavailable or failed to process',
                'message' => $e->getMessage(),
            ];
        }
    }
}
