<?php

namespace Modules\Settings\Http\Requests;

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
            default => [],
        };
    }

    private function generalRules(): array
    {
        return [
            'site_name' => 'required|string|max:100',
            'site_description' => 'nullable|string|max:500',
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
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_previous_og_image' => 'nullable|boolean',
            'twitter_handle' => 'nullable|string|max:50',
            'google_analytics_id' => 'nullable|string|max:50',
            'robots_default' => 'nullable|string|max:50',
            'canonical_domain' => 'nullable|url|max:255',
        ];
    }

    private function mailRules(): array
    {
        return [
            'from_name' => 'nullable|string|max:100',
            'from_address' => 'nullable|email|max:255',
            'host' => 'nullable|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|string|in:tls,ssl,starttls',
        ];
    }

    private function shippingRules(): array
    {
        return [
            'flat_rate' => 'required|integer|min:0',
            'free_shipping_threshold' => 'required|integer|min:0',
        ];
    }
}
