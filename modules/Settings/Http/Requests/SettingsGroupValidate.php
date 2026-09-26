<?php

namespace Modules\Settings\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Courier\Enums\CourierProvider;
use Modules\Support\Http\Requests\Request;

class SettingsGroupValidate extends Request
{
    public function rules(): array
    {
        return match ($this->route('group')) {
            'general' => $this->generalRules(),
            'branding' => $this->brandingRules(),
            'contact' => $this->contactRules(),
            'social' => $this->socialRules(),
            'seo' => $this->seoRules(),
            'mail' => $this->mailRules(),
            'shipping' => $this->shippingRules(),
            'courier' => $this->courierRules(),
            'homepage' => $this->homepageRules(),
            'pixel' => $this->pixelRules(),
            'analytics' => $this->analyticsRules(),
            'downloads' => $this->downloadsRules(),
            default => [],
        };
    }

    private function generalRules(): array
    {
        return [
            'site_description' => 'nullable|string|max:500',
            'admin_email' => 'nullable|email|max:255',
        ];
    }

    private function brandingRules(): array
    {
        return [
            'site_name' => 'required|string|max:100',
            'site_slogan' => 'nullable|string|max:200',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:512',
            'dark_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'remove_previous_logo' => 'nullable|boolean',
            'remove_previous_favicon' => 'nullable|boolean',
            'remove_previous_dark_logo' => 'nullable|boolean',
        ];
    }

    private function contactRules(): array
    {
        return [
            'phone' => 'nullable|array',
            'phone.*' => 'nullable|string|max:20',
            'email' => 'nullable|array',
            'email.*' => 'nullable|email|max:100',
            'address' => 'nullable|array',
            'address.*' => 'nullable|string|max:300',
            'whatsapp' => 'nullable|array',
            'whatsapp.*' => 'nullable|string|max:20',
            'working_hours' => 'nullable|array',
            'working_hours.*' => 'nullable|string|max:250',
            'google_map' => 'nullable|string|max:2000',
        ];
    }

    private function socialRules(): array
    {
        return [
            'facebook' => 'nullable|url|max:255',
            'x' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'github' => 'nullable|url|max:255',
        ];
    }

    private function seoRules(): array
    {
        return [
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }

    private function mailRules(): array
    {
        return [
            'enable_smtp' => 'nullable|boolean',
            'from_name' => 'nullable|string|max:100',
            'from_address' => 'nullable|email|max:255',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|string|in:tls,ssl,starttls',
        ];
    }

    private function homepageRules(): array
    {
        return [
            'show_slider' => 'nullable|boolean',
            'show_featured_products' => 'nullable|boolean',
            'show_featured_categories' => 'nullable|boolean',
            'show_blog' => 'nullable|boolean',
            'show_brands' => 'nullable|boolean',
        ];
    }

    private function downloadsRules(): array
    {
        return [
            'default_expiry_days' => 'nullable|integer|min:0',
            'default_limit' => 'nullable|integer|min:0',
        ];
    }

    private function shippingRules(): array
    {
        return [
            'options' => 'nullable|array',
            'options.*.id' => 'nullable|string|max:100',
            'options.*.name' => 'required|string|max:255',
            'options.*.price' => 'required|numeric|min:0',
            'options.*.enabled' => 'nullable|boolean',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
        ];
    }

    private function courierRules(): array
    {
        $booleans = [
            'pathao_enabled', 'pathao_sandbox',
            'steadfast_enabled',
            'redx_enabled', 'redx_sandbox',
            'ecourier_enabled', 'ecourier_sandbox',
            'paperfly_enabled',
            'fraud_enabled',
        ];

        $texts = [
            'pathao_client_id', 'pathao_client_secret', 'pathao_username', 'pathao_password', 'pathao_store_id',
            'pathao_webhook_secret', 'pathao_tracking_url',
            'steadfast_api_key', 'steadfast_secret_key', 'steadfast_webhook_secret', 'steadfast_tracking_url',
            'redx_access_token', 'redx_webhook_secret', 'redx_tracking_url',
            'ecourier_api_key', 'ecourier_api_secret', 'ecourier_user_id', 'ecourier_webhook_secret', 'ecourier_tracking_url',
            'paperfly_username', 'paperfly_password', 'paperfly_api_key', 'paperfly_webhook_secret', 'paperfly_tracking_url',
            'fraud_steadfast_user', 'fraud_steadfast_password',
            'fraud_pathao_user', 'fraud_pathao_password',
            'fraud_redx_phone', 'fraud_redx_password',
            'fraud_paperfly_user', 'fraud_paperfly_password',
            'fraud_carrybee_phone', 'fraud_carrybee_password',
        ];

        $rules = [
            'default_courier' => ['nullable', Rule::in(CourierProvider::values())],
            'default_weight_kg' => 'nullable|numeric|min:0.1|max:100',
            'fraud_min_deliveries' => 'nullable|integer|min:0|max:10000',
            'fraud_cancel_ratio_threshold' => 'nullable|numeric|min:0|max:100',
            'steadfast_base_url' => ['nullable', 'url', 'max:500'],
        ];

        foreach ($booleans as $key) {
            $rules[$key] = 'nullable|boolean';
        }

        foreach ($texts as $key) {
            $rules[$key] = 'nullable|string|max:500';
        }

        return $rules;
    }

    private function pixelRules(): array
    {
        return [
            'enabled' => 'nullable|boolean',
            'meta_pixel_id' => ['nullable', 'regex:/^\d{8,20}$/'],
            'require_consent' => 'nullable|boolean',
            'enable_non_production' => 'nullable|boolean',
            'capi_enabled' => 'nullable|boolean',
            'capi_access_token' => 'nullable|string|max:500',
            'api_version' => ['nullable', 'regex:/^v\d+\.\d+$/'],
            'test_event_code' => 'nullable|string|max:100',
        ];
    }

    private function analyticsRules(): array
    {
        return [
            'enabled' => 'nullable|boolean',
            'ga_measurement_id' => ['nullable', 'string', 'regex:/^G-[A-Z0-9]+$/i'],
            'gtm_container_id' => ['nullable', 'string', 'regex:/^GTM-[A-Z0-9]+$/i'],
        ];
    }
}
