<?php

namespace Modules\Courier\Services;

use Modules\Courier\Enums\CourierProvider;

/**
 * Applies courier settings (admin Settings page) onto the CourierHub and
 * fraud-checker config repositories at runtime. Must run before any courier
 * driver or fraud service is resolved, including inside queued jobs where the
 * application was booted before settings changed.
 */
class CourierConfigHydrator
{
    /**
     * @var array<string, array<string, string>> setting key => config path under courierhub.couriers.{provider}
     */
    private const CREDENTIAL_MAP = [
        'pathao' => [
            'pathao_sandbox' => 'sandbox',
            'pathao_client_id' => 'client_id',
            'pathao_client_secret' => 'client_secret',
            'pathao_username' => 'username',
            'pathao_password' => 'password',
            'pathao_store_id' => 'default_store_id',
        ],
        'steadfast' => [
            'steadfast_api_key' => 'api_key',
            'steadfast_secret_key' => 'secret_key',
            'steadfast_base_url' => 'base_url',
        ],
        'redx' => [
            'redx_sandbox' => 'sandbox',
            'redx_access_token' => 'access_token',
        ],
        'ecourier' => [
            'ecourier_sandbox' => 'sandbox',
            'ecourier_api_key' => 'api_key',
            'ecourier_api_secret' => 'api_secret',
            'ecourier_user_id' => 'user_id',
        ],
        'paperfly' => [
            'paperfly_username' => 'username',
            'paperfly_password' => 'password',
            'paperfly_api_key' => 'api_key',
        ],
    ];

    /**
     * @var array<string, string> setting key => config path under fraud-checker-bd-courier
     */
    private const FRAUD_MAP = [
        'fraud_steadfast_user' => 'steadfast.user',
        'fraud_steadfast_password' => 'steadfast.password',
        'fraud_pathao_user' => 'pathao.user',
        'fraud_pathao_password' => 'pathao.password',
        'fraud_redx_phone' => 'redx.phone',
        'fraud_redx_password' => 'redx.password',
        'fraud_paperfly_user' => 'paperfly.user',
        'fraud_paperfly_password' => 'paperfly.password',
        'fraud_carrybee_phone' => 'carrybee.phone',
        'fraud_carrybee_password' => 'carrybee.password',
    ];

    public function hydrate(): void
    {
        $settings = settings_group('courier');

        if ($settings === []) {
            return;
        }

        if (! empty($settings['default_courier'])) {
            config(['courierhub.default' => (string) $settings['default_courier']]);
        }

        foreach (CourierProvider::cases() as $provider) {
            $key = $provider->value;

            config(["courierhub.couriers.{$key}.enabled" => $this->isTruthy($settings["{$key}_enabled"] ?? null)]);

            foreach (self::CREDENTIAL_MAP[$key] as $settingKey => $configKey) {
                $this->setWhenPresent("courierhub.couriers.{$key}.{$configKey}", $settings[$settingKey] ?? null);
            }

            $this->setWhenPresent("courierhub.webhook.secrets.{$key}", $settings["{$key}_webhook_secret"] ?? null);
        }

        foreach (self::FRAUD_MAP as $settingKey => $configKey) {
            $this->setWhenPresent("fraud-checker-bd-courier.{$configKey}", $settings[$settingKey] ?? null);
        }
    }

    private function setWhenPresent(string $configKey, mixed $value): void
    {
        if ($value !== null && $value !== '') {
            config([$configKey => $value]);
        }
    }

    /**
     * Boolean settings are cast to real booleans by the Setting model, while
     * seeded defaults may still arrive as strings.
     */
    private function isTruthy(mixed $value): bool
    {
        return $value === true || $value === '1' || $value === 1;
    }
}
