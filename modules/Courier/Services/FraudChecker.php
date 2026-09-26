<?php

namespace Modules\Courier\Services;

use Azmolla\FraudCheckerBdCourier\Contracts\CourierServiceInterface;
use Azmolla\FraudCheckerBdCourier\Services\CarrybeeService;
use Azmolla\FraudCheckerBdCourier\Services\PaperflyService;
use Azmolla\FraudCheckerBdCourier\Services\PathaoService;
use Azmolla\FraudCheckerBdCourier\Services\RedxService;
use Azmolla\FraudCheckerBdCourier\Services\SteadfastService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Aggregates COD cancel history from the configured fraud portals.
 *
 * Unlike the package's FraudCheckerBdCourierManager — which resolves all five
 * courier services eagerly and therefore explodes when a single portal lacks
 * credentials — this builds each service on demand, skips unconfigured
 * portals, and always returns the same payload shape.
 *
 * SteadFast is queried through the documented authenticated API
 * (GET /fraud_check/score/{phone} with the existing API keys) instead of the
 * package's portal scraper: the older fraud-count endpoint stops returning
 * counts on 27 September 2026, and the web-portal scrape depends on separate
 * credentials that the API keys already replace. The other couriers still
 * use their portal scrapers.
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
     * Representative parcel counts per documented volume band, used because
     * the score endpoint reports bands instead of exact delivery counts.
     *
     * @var array<string, int>
     */
    private const VOLUME_BAND_DELIVERIES = [
        'none' => 0,
        'low' => 5,
        'medium' => 20,
        'high' => 200,
        'very_high' => 250,
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
                $stats = $key === 'steadfast'
                    ? $this->steadfastApiScore($phone)
                    : (new $class)->getDeliveryStats($phone);
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

    /**
     * GET /fraud_check/score/{phone} using the courier API keys — no portal
     * login required. Deliberately sent without HTTP retries so a rejected
     * key can never contribute to the courier's auth-failure lockout budget.
     *
     * @return array<string, mixed> package-shaped stats or an error entry
     */
    private function steadfastApiScore(string $phone): array
    {
        $baseUrl = (string) config('courierhub.couriers.steadfast.base_url');
        $apiKey = (string) config('courierhub.couriers.steadfast.api_key');
        $secretKey = (string) config('courierhub.couriers.steadfast.secret_key');

        if ($baseUrl === '' || $apiKey === '' || $secretKey === '') {
            return ['error' => 'Not configured'];
        }

        try {
            $response = Http::withHeaders([
                'Api-Key' => $apiKey,
                'Secret-Key' => $secretKey,
            ])
                ->acceptJson()
                ->timeout(15)
                ->get(rtrim($baseUrl, '/').'/fraud_check/score/'.$phone);
        } catch (\Throwable $e) {
            return [
                'error' => 'Service unavailable or failed to process',
                'message' => $e->getMessage(),
            ];
        }

        if ($response->failed()) {
            return [
                'error' => 'Service unavailable or failed to process',
                'status' => $response->status(),
            ];
        }

        return $this->mapScoreResponse((array) $response->json());
    }

    /**
     * Converts the score endpoint's band/ratio vocabulary into the
     * success/cancel counts the aggregate expects, while keeping the raw
     * score fields for the order's fraud_details.
     *
     * @return array<string, mixed>
     */
    private function mapScoreResponse(array $data): array
    {
        $deliveryRatio = is_numeric($data['delivery_ratio'] ?? null) ? (float) $data['delivery_ratio'] : null;
        $cancellationRatio = is_numeric($data['cancellation_ratio'] ?? null) ? (float) $data['cancellation_ratio'] : null;

        // The documented ratios are percentages; a pair summing to at most
        // 1.5 can only be a 0–1 fraction, so scale it up to percent.
        if ($deliveryRatio !== null && $cancellationRatio !== null && ($deliveryRatio + $cancellationRatio) <= 1.5) {
            $deliveryRatio *= 100;
            $cancellationRatio *= 100;
        }

        $band = strtolower((string) ($data['volume_band'] ?? 'none'));
        $total = self::VOLUME_BAND_DELIVERIES[$band] ?? 0;
        $cancellationRatio = min(max($cancellationRatio ?? 0.0, 0.0), 100.0);
        $cancel = (int) round($total * $cancellationRatio / 100);

        return [
            'success' => max(0, $total - $cancel),
            'cancel' => $cancel,
            'total' => $total,
            'success_ratio' => $total > 0 ? round(((max(0, $total - $cancel)) / $total) * 100, 2) : 0.0,
            'cancellation_ratio' => $cancellationRatio,
            'delivery_ratio' => $deliveryRatio,
            'volume_band' => $data['volume_band'] ?? null,
            'total_reports' => (int) ($data['total_reports'] ?? 0),
            'fraud_categories' => is_array($data['fraud_categories'] ?? null) ? $data['fraud_categories'] : [],
            'source' => 'steadfast-api',
        ];
    }
}
