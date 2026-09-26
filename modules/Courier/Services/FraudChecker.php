<?php

namespace Modules\Courier\Services;

use Azmolla\FraudCheckerBdCourier\Contracts\CourierServiceInterface;
use Azmolla\FraudCheckerBdCourier\Services\CarrybeeService;
use Azmolla\FraudCheckerBdCourier\Services\PaperflyService;
use Azmolla\FraudCheckerBdCourier\Services\PathaoService;
use Azmolla\FraudCheckerBdCourier\Services\RedxService;
use Azmolla\FraudCheckerBdCourier\Services\SteadfastService;
use Illuminate\Support\Facades\Log;

/**
 * Aggregates COD cancel history from the configured fraud portals.
 *
 * Unlike the package's FraudCheckerBdCourierManager — which resolves all five
 * courier services eagerly and therefore explodes when a single portal lacks
 * credentials — this builds each service on demand, skips unconfigured
 * portals, and always returns the same payload shape.
 */
class FraudChecker
{
    /**
     * Portal key => package service class.
     *
     * @var array<string, class-string<CourierServiceInterface>>
     */
    private const PORTALS = [
        'steadfast' => SteadfastService::class,
        'pathao' => PathaoService::class,
        'redx' => RedxService::class,
        'paperfly' => PaperflyService::class,
        'carrybee' => CarrybeeService::class,
    ];

    /**
     * @return array<string, mixed> per-portal stats plus an aggregate summary,
     *                              shaped like the package manager's payload
     */
    public function check(string $phone): array
    {
        $payload = [
            'steadfast' => null,
            'pathao' => null,
            'redx' => null,
            'paperfly' => null,
            'carrybee' => null,
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

        foreach (self::PORTALS as $key => $class) {
            try {
                $service = new $class;
            } catch (\Throwable) {
                $payload[$key] = ['error' => 'Not configured'];

                continue;
            }

            try {
                $stats = $service->getDeliveryStats($phone);
                $payload[$key] = $stats;

                if (isset($stats['success'], $stats['cancel'])
                    && is_numeric($stats['success'])
                    && is_numeric($stats['cancel'])) {
                    $totalSuccess += (int) $stats['success'];
                    $totalCancel += (int) $stats['cancel'];
                }
            } catch (\Throwable $e) {
                Log::error("FraudChecker: {$key} portal failed.", [
                    'message' => $e->getMessage(),
                    'phone' => $phone,
                ]);

                $payload[$key] = [
                    'error' => 'Service unavailable or failed to process',
                    'message' => $e->getMessage(),
                ];
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
     * Number of portals that actually answered with stats (configured and reachable).
     *
     * @param  array<string, mixed>  $payload
     */
    public static function answeredPortals(array $payload): int
    {
        return collect($payload)
            ->except('aggregate')
            ->filter(fn ($value) => is_array($value) && ! array_key_exists('error', $value))
            ->count();
    }
}
