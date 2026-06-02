<?php

namespace Modules\Settings\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaConversionApiService
{
    /**
     * @param  array<string, mixed>  $customData
     * @param  array<string, mixed>  $context
     */
    public function track(string $eventName, array $customData = [], array $context = []): void
    {
        if (! $this->isEnabled($context)) {
            return;
        }

        $pixelId = (string) setting('pixel.meta_pixel_id', '');
        $accessToken = (string) setting('pixel.capi_access_token', '');
        $apiVersion = (string) setting('pixel.api_version', 'v23.0');
        $testEventCode = (string) setting('pixel.test_event_code', '');

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => now()->timestamp,
                    'event_id' => $context['event_id'] ?? null,
                    'event_source_url' => $context['event_source_url'] ?? null,
                    'action_source' => 'website',
                    'user_data' => $this->buildUserData($context),
                    'custom_data' => $customData,
                ],
            ],
        ];

        if (! empty($testEventCode) && (! app()->environment('production') || ! empty($context['force_test_code']))) {
            $payload['test_event_code'] = $testEventCode;
        }

        try {
            $response = Http::asJson()
                ->timeout(5)
                ->retry(2, 200)
                ->post("https://graph.facebook.com/{$apiVersion}/{$pixelId}/events", [
                    ...$payload,
                    'access_token' => $accessToken,
                ]);

            if ($response->failed()) {
                Log::warning('Meta CAPI request failed', [
                    'event_name' => $eventName,
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::warning('Meta CAPI request exception', [
                'event_name' => $eventName,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function isEnabled(array $context): bool
    {
        $enabled = (bool) setting('pixel.enabled', false);
        $capiEnabled = (bool) setting('pixel.capi_enabled', true);
        $requireConsent = (bool) setting('pixel.require_consent', true);
        $allowNonProduction = (bool) setting('pixel.enable_non_production', false);
        $pixelId = (string) setting('pixel.meta_pixel_id', '');
        $accessToken = (string) setting('pixel.capi_access_token', '');

        if (! $enabled || ! $capiEnabled || $pixelId === '' || $accessToken === '') {
            return false;
        }

        if (! app()->environment('production') && ! $allowNonProduction) {
            return false;
        }

        if ($requireConsent && ! ($context['consent_granted'] ?? false)) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function buildUserData(array $context): array
    {
        $userData = [
            'client_ip_address' => $context['client_ip_address'] ?? null,
            'client_user_agent' => $context['client_user_agent'] ?? null,
            'fbp' => $context['fbp'] ?? null,
            'fbc' => $context['fbc'] ?? null,
            'em' => $this->hashEmail($context['email'] ?? null),
            'ph' => $this->hashPhone($context['phone'] ?? null),
            'fn' => $this->hashPlain($context['first_name'] ?? null),
            'ln' => $this->hashPlain($context['last_name'] ?? null),
            'external_id' => $this->hashPlain($context['external_id'] ?? null),
        ];

        return array_filter($userData, static fn ($value) => ! is_null($value) && $value !== '');
    }

    private function hashEmail(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return hash('sha256', strtolower(trim($value)));
    }

    private function hashPhone(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $normalized = preg_replace('/\D+/', '', $value) ?? '';
        if ($normalized === '') {
            return null;
        }

        return hash('sha256', $normalized);
    }

    private function hashPlain(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return hash('sha256', strtolower(trim($value)));
    }
}
