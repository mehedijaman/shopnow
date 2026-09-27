<?php

namespace Modules\Courier\Services\Fraud\Providers;

use Illuminate\Support\Facades\Http;
use Modules\Courier\Services\Fraud\Contracts\FraudProviderInterface;

/**
 * Aggregates per-courier delivery stats and fraud reports for a phone
 * number through the BD Courier API (POST /courier-check, Bearer auth).
 */
class BdCourierFraudProvider implements FraudProviderInterface
{
    public function name(): string
    {
        return 'bdcourier';
    }

    public function enabled(): bool
    {
        return filter_var(config('fraud.bdcourier.enabled'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    public function configured(): bool
    {
        return (string) config('fraud.bdcourier.base_url') !== ''
            && (string) config('fraud.bdcourier.api_key') !== '';
    }

    public function check(string $phone): array
    {
        $baseUrl = rtrim((string) config('fraud.bdcourier.base_url'), '/');
        $apiKey = (string) config('fraud.bdcourier.api_key');

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->post($baseUrl.'/courier-check', ['phone' => $phone]);
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

        $json = (array) $response->json();

        if (($json['status'] ?? null) !== 'success') {
            return [
                'error' => 'Service unavailable or failed to process',
                'message' => (string) ($json['message'] ?? 'Unexpected response'),
            ];
        }

        return $this->mapResponse((array) ($json['data'] ?? []));
    }

    /**
     * Maps the response's summary to the shared success/cancel counts while
     * keeping the per-courier breakdown and fraud reports for the UI.
     *
     * @return array<string, mixed>
     */
    private function mapResponse(array $data): array
    {
        $summary = (array) ($data['summary'] ?? []);
        $total = (int) ($summary['total_parcel'] ?? 0);
        $success = (int) ($summary['success_parcel'] ?? 0);
        $cancel = (int) ($summary['cancelled_parcel'] ?? 0);

        $couriers = [];

        foreach ($data as $key => $value) {
            if (! is_string($key) || ! is_array($value) || in_array($key, ['summary', 'reports'], true)) {
                continue;
            }

            $couriers[$key] = [
                'name' => $value['name'] ?? null,
                'logo' => $value['logo'] ?? null,
                'total_parcel' => (int) ($value['total_parcel'] ?? 0),
                'success_parcel' => (int) ($value['success_parcel'] ?? 0),
                'cancelled_parcel' => (int) ($value['cancelled_parcel'] ?? 0),
                'success_ratio' => (float) ($value['success_ratio'] ?? 0),
            ];
        }

        return [
            'success' => $success,
            'cancel' => $cancel,
            'total' => $total,
            'success_ratio' => $total > 0
                ? (float) ($summary['success_ratio'] ?? round(($success / $total) * 100, 2))
                : 0.0,
            'couriers' => $couriers,
            'reports' => array_values(is_array($data['reports'] ?? null) ? $data['reports'] : []),
            'source' => 'bdcourier-api',
        ];
    }
}
