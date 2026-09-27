<?php

namespace Modules\Courier\Services\Fraud\Providers;

use Illuminate\Support\Facades\Http;
use Modules\Courier\Services\Fraud\Contracts\FraudProviderInterface;

/**
 * Queries the documented SteadFast fraud score endpoint
 * (GET /fraud_check/score/{phone}) with the courier API keys — no portal
 * login required. Deliberately sent without HTTP retries so a rejected key
 * can never contribute to the courier's auth-failure lockout budget.
 */
class SteadfastFraudProvider implements FraudProviderInterface
{
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

    public function name(): string
    {
        return 'steadfast';
    }

    public function enabled(): bool
    {
        return filter_var(config('fraud.steadfast.enabled'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    public function configured(): bool
    {
        return (string) config('courierhub.couriers.steadfast.base_url') !== ''
            && (string) config('courierhub.couriers.steadfast.api_key') !== ''
            && (string) config('courierhub.couriers.steadfast.secret_key') !== '';
    }

    public function check(string $phone): array
    {
        $baseUrl = rtrim((string) config('courierhub.couriers.steadfast.base_url'), '/');
        $apiKey = (string) config('courierhub.couriers.steadfast.api_key');
        $secretKey = (string) config('courierhub.couriers.steadfast.secret_key');

        try {
            $response = Http::withHeaders([
                'Api-Key' => $apiKey,
                'Secret-Key' => $secretKey,
            ])
                ->acceptJson()
                ->timeout(15)
                ->get($baseUrl.'/fraud_check/score/'.$phone);
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
