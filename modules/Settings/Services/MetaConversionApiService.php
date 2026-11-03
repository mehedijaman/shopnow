<?php

namespace Modules\Settings\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Customer\Models\Customer;

class MetaConversionApiService
{
    public function trackRegistration(Customer $customer, Request $request): void
    {
        $this->track('CompleteRegistration', [
            'status' => true,
            'content_name' => 'Customer Signup',
        ], [
            'event_id' => 'registration_'.$customer->id,
            'event_source_url' => url('/signup'),
            'consent_granted' => true,
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
            'fbp' => $request->cookie('_fbp'),
            'fbc' => $request->cookie('_fbc'),
            'email' => $customer->email,
            'phone' => $customer->phone,
            'first_name' => $customer->name,
            'external_id' => (string) $customer->id,
        ]);
    }

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
        $allowNonProduction = (bool) setting('pixel.enable_non_production', false);
        $pixelId = (string) setting('pixel.meta_pixel_id', '');
        $accessToken = (string) setting('pixel.capi_access_token', '');

        if (! $enabled || ! $capiEnabled || $pixelId === '' || $accessToken === '') {
            return false;
        }

        if (! app()->environment('production') && ! $allowNonProduction) {
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
