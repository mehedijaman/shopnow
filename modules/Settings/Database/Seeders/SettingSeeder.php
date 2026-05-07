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
        $permissions = ['settings-list', 'settings-edit'];

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
            ['group' => 'general', 'key' => 'site_name', 'value' => 'ShopNow', 'type' => 'text', 'label' => 'Site Name', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'general', 'key' => 'site_description', 'value' => null, 'type' => 'textarea', 'label' => 'Site Description', 'is_public' => true, 'sort_order' => 2],

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

            // Mail
            ['group' => 'mail', 'key' => 'from_name', 'value' => null, 'type' => 'text', 'label' => 'From Name', 'is_public' => false, 'sort_order' => 1],
            ['group' => 'mail', 'key' => 'from_address', 'value' => null, 'type' => 'text', 'label' => 'From Address', 'is_public' => false, 'sort_order' => 2],
            ['group' => 'mail', 'key' => 'host', 'value' => null, 'type' => 'text', 'label' => 'SMTP Host', 'is_public' => false, 'sort_order' => 3],
            ['group' => 'mail', 'key' => 'port', 'value' => null, 'type' => 'text', 'label' => 'SMTP Port', 'is_public' => false, 'sort_order' => 4],
            ['group' => 'mail', 'key' => 'username', 'value' => null, 'type' => 'text', 'label' => 'SMTP Username', 'is_public' => false, 'sort_order' => 5],
            ['group' => 'mail', 'key' => 'password', 'value' => null, 'type' => 'text', 'label' => 'SMTP Password', 'is_public' => false, 'sort_order' => 6],
            ['group' => 'mail', 'key' => 'encryption', 'value' => 'tls', 'type' => 'text', 'label' => 'Encryption', 'is_public' => false, 'sort_order' => 7],
        ];
    }
}
