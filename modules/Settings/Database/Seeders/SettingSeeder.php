<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Setting;
use Spatie\Permission\Models\Permission;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedSettings();
    }

    private function seedPermissions(): void
    {
        $permissions = ['settings-list', 'settings-edit', 'pixel-settings-edit'];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'user'],
            );
        }
    }

    private function seedSettings(): void
    {
        foreach ($this->getDefaultSettings() as $setting) {
            Setting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting,
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function getDefaultSettings(): array
    {
        return [
            // General
            ['group' => 'general', 'key' => 'site_description', 'value' => null, 'type' => 'textarea', 'label' => 'Site Description', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'general', 'key' => 'admin_email', 'value' => null, 'type' => 'text', 'label' => 'Admin Email', 'description' => 'Receives order notification emails.', 'is_public' => false, 'sort_order' => 2],

            // Branding
            ['group' => 'branding', 'key' => 'site_name', 'value' => 'ShopNow', 'type' => 'text', 'label' => 'Site Name', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'branding', 'key' => 'site_slogan', 'value' => null, 'type' => 'text', 'label' => 'Site Slogan', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'branding', 'key' => 'logo', 'value' => null, 'type' => 'image', 'label' => 'Logo', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'branding', 'key' => 'favicon', 'value' => null, 'type' => 'image', 'label' => 'Favicon', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'branding', 'key' => 'dark_logo', 'value' => null, 'type' => 'image', 'label' => 'Dark Logo', 'is_public' => true, 'sort_order' => 5],

            // Contact
            ['group' => 'contact', 'key' => 'phone', 'value' => '[]', 'type' => 'repeater', 'label' => 'Phone Numbers', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'contact', 'key' => 'email', 'value' => '[]', 'type' => 'repeater', 'label' => 'Email Addresses', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'contact', 'key' => 'address', 'value' => '[]', 'type' => 'repeater', 'label' => 'Office Addresses', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'contact', 'key' => 'whatsapp', 'value' => '[]', 'type' => 'repeater', 'label' => 'WhatsApp Numbers', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'contact', 'key' => 'google_map', 'value' => null, 'type' => 'textarea', 'label' => 'Google Map Embed', 'is_public' => true, 'sort_order' => 5],

            // Social
            ['group' => 'social', 'key' => 'facebook', 'value' => null, 'type' => 'text', 'label' => 'Facebook', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'social', 'key' => 'x', 'value' => null, 'type' => 'text', 'label' => 'X (Twitter)', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'social', 'key' => 'instagram', 'value' => null, 'type' => 'text', 'label' => 'Instagram', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'social', 'key' => 'youtube', 'value' => null, 'type' => 'text', 'label' => 'YouTube', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'social', 'key' => 'linkedin', 'value' => null, 'type' => 'text', 'label' => 'LinkedIn', 'is_public' => true, 'sort_order' => 5],
            ['group' => 'social', 'key' => 'tiktok', 'value' => null, 'type' => 'text', 'label' => 'TikTok', 'is_public' => true, 'sort_order' => 6],
            ['group' => 'social', 'key' => 'github', 'value' => null, 'type' => 'text', 'label' => 'GitHub', 'is_public' => true, 'sort_order' => 7],

            // SEO
            ['group' => 'seo', 'key' => 'meta_title', 'value' => null, 'type' => 'text', 'label' => 'Meta Title', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => null, 'type' => 'textarea', 'label' => 'Meta Description', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'seo', 'key' => 'meta_keywords', 'value' => null, 'type' => 'text', 'label' => 'Meta Keywords', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'seo', 'key' => 'og_image', 'value' => null, 'type' => 'image', 'label' => 'Default OG/Social Share Image', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'seo', 'key' => 'twitter_handle', 'value' => null, 'type' => 'text', 'label' => 'Twitter/X Handle (e.g. @shopnow)', 'is_public' => true, 'sort_order' => 5],
            ['group' => 'seo', 'key' => 'google_analytics_id', 'value' => null, 'type' => 'text', 'label' => 'Google Analytics ID (G-XXXXXX)', 'is_public' => true, 'sort_order' => 6],
            ['group' => 'seo', 'key' => 'robots_default', 'value' => 'index, follow', 'type' => 'text', 'label' => 'Default Robots Directive', 'is_public' => false, 'sort_order' => 7],
            ['group' => 'seo', 'key' => 'canonical_domain', 'value' => null, 'type' => 'text', 'label' => 'Canonical Domain (e.g. https://example.com)', 'is_public' => false, 'sort_order' => 8],

            // Mail
            ['group' => 'mail', 'key' => 'from_name', 'value' => null, 'type' => 'text', 'label' => 'From Name', 'is_public' => false, 'sort_order' => 1],
            ['group' => 'mail', 'key' => 'from_address', 'value' => null, 'type' => 'text', 'label' => 'From Address', 'is_public' => false, 'sort_order' => 2],
            ['group' => 'mail', 'key' => 'host', 'value' => null, 'type' => 'text', 'label' => 'SMTP Host', 'is_public' => false, 'sort_order' => 3],
            ['group' => 'mail', 'key' => 'port', 'value' => null, 'type' => 'text', 'label' => 'SMTP Port', 'is_public' => false, 'sort_order' => 4],
            ['group' => 'mail', 'key' => 'username', 'value' => null, 'type' => 'text', 'label' => 'SMTP Username', 'is_public' => false, 'sort_order' => 5],
            ['group' => 'mail', 'key' => 'password', 'value' => null, 'type' => 'text', 'label' => 'SMTP Password', 'is_public' => false, 'sort_order' => 6],
            ['group' => 'mail', 'key' => 'encryption', 'value' => 'tls', 'type' => 'text', 'label' => 'Encryption', 'is_public' => false, 'sort_order' => 7],

            // Shipping
            ['group' => 'shipping', 'key' => 'flat_rate', 'value' => '60', 'type' => 'text', 'label' => 'Flat Rate Shipping (Tk)', 'description' => 'Default shipping charge added to every order.', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'shipping', 'key' => 'free_shipping_threshold', 'value' => '1000', 'type' => 'text', 'label' => 'Free Shipping Threshold (Tk)', 'description' => 'Orders above this amount get free shipping. Set 0 to disable.', 'is_public' => true, 'sort_order' => 2],

            // Homepage
            ['group' => 'homepage', 'key' => 'show_slider', 'value' => '1', 'type' => 'boolean', 'label' => 'Show Slider', 'description' => 'Show the hero image carousel on the homepage.', 'is_public' => false, 'sort_order' => 1],
            ['group' => 'homepage', 'key' => 'show_featured_categories', 'value' => '1', 'type' => 'boolean', 'label' => 'Show Featured Categories', 'description' => 'Show the featured product categories section.', 'is_public' => false, 'sort_order' => 2],
            ['group' => 'homepage', 'key' => 'show_blog', 'value' => '1', 'type' => 'boolean', 'label' => 'Show Blog Section', 'description' => 'Show the latest blog posts section.', 'is_public' => false, 'sort_order' => 3],

            // Pixel
            ['group' => 'pixel', 'key' => 'enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable Meta Pixel', 'description' => 'Turn Meta Pixel tracking on for storefront pages.', 'is_public' => false, 'sort_order' => 1],
            ['group' => 'pixel', 'key' => 'meta_pixel_id', 'value' => null, 'type' => 'text', 'label' => 'Meta Pixel ID', 'description' => 'Numeric Pixel ID from Meta Events Manager.', 'is_public' => false, 'sort_order' => 2],
            ['group' => 'pixel', 'key' => 'require_consent', 'value' => '1', 'type' => 'boolean', 'label' => 'Require Consent', 'description' => 'Do not fire pixel events until user grants tracking consent.', 'is_public' => false, 'sort_order' => 3],
            ['group' => 'pixel', 'key' => 'enable_non_production', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable in Non-Production', 'description' => 'Allow tracking on local/staging environments.', 'is_public' => false, 'sort_order' => 4],
            ['group' => 'pixel', 'key' => 'capi_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Enable Conversions API (CAPI)', 'description' => 'Send server-side events to Meta CAPI.', 'is_public' => false, 'sort_order' => 5],
            ['group' => 'pixel', 'key' => 'capi_access_token', 'value' => null, 'type' => 'text', 'label' => 'CAPI Access Token', 'description' => 'System user token for server-side Meta events.', 'is_public' => false, 'sort_order' => 6],
            ['group' => 'pixel', 'key' => 'api_version', 'value' => 'v23.0', 'type' => 'text', 'label' => 'Meta Graph API Version', 'description' => 'Meta Graph API version used for CAPI calls (e.g. v23.0).', 'is_public' => false, 'sort_order' => 7],
            ['group' => 'pixel', 'key' => 'test_event_code', 'value' => null, 'type' => 'text', 'label' => 'Test Event Code', 'description' => 'Optional Meta test_event_code for non-production verification.', 'is_public' => false, 'sort_order' => 8],
        ];
    }
}
